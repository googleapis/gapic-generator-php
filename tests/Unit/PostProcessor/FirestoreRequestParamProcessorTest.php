<?php
/*
 * Copyright 2023 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
declare(strict_types=1);

namespace Google\Generator\Tests\Unit\PostProcessor;

use PHPUnit\Framework\TestCase;
use Google\PostProcessor\FirestoreRequestParamProcessor;
use LogicException;
use ParseError;

/**
 * Test suite for FirestoreRequestParamProcessor.
 */
final class FirestoreRequestParamProcessorTest extends TestCase
{
    // Sample class with the database parameter typo
    private string $classWithTypo = <<<'EOL'
<?php

namespace Testing\Firestore;

class FirestoreClient
{
    /**
     * Connect to the database.
     * 
     * @param string $datbase The database name to connect to
     * @param array $options Connection options
     * @return bool True if connection successful
     */
    public function connect(string $datbase, array $options = []): bool
    {
        $this->currentDatabase = $datbase;
        return $this->doConnect($datbase, $options);
    }
    
    /**
     * Get current database name.
     * 
     * @return string Current database name
     */
    public function getCurrentDatabase(): string
    {
        return $this->currentDatabase ?? '';
    }
}
EOL;

    // Sample class with correct parameter names (already fixed)
    private string $classWithoutTypo = <<<'EOL'
<?php

namespace Testing\Firestore;

class FirestoreClient
{
    /**
     * Connect to the database.
     * 
     * @param string $database The database name to connect to
     * @param array $options Connection options
     * @return bool True if connection successful
     */
    public function connect(string $database, array $options = []): bool
    {
        $this->currentDatabase = $database;
        return $this->doConnect($database, $options);
    }
}
EOL;

    // Sample class without Firestore-related content
    private string $nonFirestoreClass = <<<'EOL'
<?php

namespace Testing\Other;

class RegularClient
{
    public function doSomething(): void
    {
        echo "Nothing to do with Firestore";
    }
}
EOL;

    // Sample with only PHPDoc typo
    private string $classWithPhpDocTypoOnly = <<<'EOL'
<?php

namespace Testing\Firestore;

class FirestoreClient
{
    /**
     * @param string $datbase The database name
     */
    public function connect(string $database): void
    {
        // Method parameter is correct, only PHPDoc has typo
    }
}
EOL;

    // Sample with syntax error (unbalanced braces)
    private string $classWithSyntaxError = <<<'EOL'
<?php

namespace Testing\Firestore;

class FirestoreClient
{
    public function connect(string $datbase): void
    {
        // Missing closing brace
EOL;

    // Sample with no class
    private string $noClassContent = <<<'EOL'
<?php

function someFunction() {
    return true;
}
EOL;

    /**
     * Test successful fixing of database parameter typo.
     */
    public function testFixDatabaseParameterTypo(): void
    {
        $processor = new FirestoreRequestParamProcessor($this->classWithTypo, 'test.php');
        $processor->fixDatabaseParameterIssues();
        
        $result = $processor->getContents();
        $this->assertTrue($processor->hasChanges());
        
        // Verify PHPDoc was fixed
        $this->assertMatchesRegularExpression('/@param\s+string\s+\$database\s+The database name/', $result);
        $this->assertDoesNotMatchRegularExpression('/@param\s+string\s+\$datbase/', $result);
        
        // Verify method parameter was fixed
        $this->assertStringContainsString('function connect(string $database', $result);
        $this->assertStringNotContainsString('$datbase', $result);
        
        // Verify variable references in method body were fixed
        $this->assertStringContainsString('$this->currentDatabase = $database;', $result);
        $this->assertStringContainsString('$this->doConnect($database, $options)', $result);
    }

    /**
     * Test that processor is idempotent (multiple runs don't duplicate changes).
     */
    public function testIdempotentOperation(): void
    {
        // First pass
        $processor1 = new FirestoreRequestParamProcessor($this->classWithTypo, 'test.php');
        $processor1->fixDatabaseParameterIssues();
        $firstResult = $processor1->getContents();
        $this->assertTrue($processor1->hasChanges());
        
        // Second pass on already fixed content
        $processor2 = new FirestoreRequestParamProcessor($firstResult, 'test.php');
        $processor2->fixDatabaseParameterIssues();
        $secondResult = $processor2->getContents();
        $this->assertFalse($processor2->hasChanges());
        
        // Results should be identical
        $this->assertEquals($firstResult, $secondResult);
    }

    /**
     * Test processing class that already has correct parameter names.
     */
    public function testClassWithoutTypo(): void
    {
        $processor = new FirestoreRequestParamProcessor($this->classWithoutTypo, 'test.php');
        $processor->fixDatabaseParameterIssues();
        
        $result = $processor->getContents();
        $this->assertFalse($processor->hasChanges());
        $this->assertEquals($this->classWithoutTypo, $result);
    }

    /**
     * Test processing non-Firestore class (should be skipped in real usage).
     */
    public function testNonFirestoreClass(): void
    {
        $processor = new FirestoreRequestParamProcessor($this->nonFirestoreClass, 'test.php');
        $processor->fixDatabaseParameterIssues();
        
        $result = $processor->getContents();
        $this->assertFalse($processor->hasChanges());
        $this->assertEquals($this->nonFirestoreClass, $result);
    }

    /**
     * Test fixing only PHPDoc typo when method parameter is correct.
     */
    public function testPhpDocTypoOnly(): void
    {
        $processor = new FirestoreRequestParamProcessor($this->classWithPhpDocTypoOnly, 'test.php');
        $processor->fixDatabaseParameterIssues();
        
        $result = $processor->getContents();
        $this->assertTrue($processor->hasChanges());
        
        // Verify PHPDoc was fixed
        $this->assertStringContainsString('@param string $database The database name', $result);
        $this->assertStringNotContainsString('$datbase', $result);
        
        // Verify method parameter remains correct
        $this->assertStringContainsString('function connect(string $database)', $result);
    }

    /**
     * Test exception when constructor receives invalid PHP syntax.
     */
    public function testConstructorWithSyntaxError(): void
    {
        $this->expectException(ParseError::class);
        $this->expectExceptionMessageMatches('/PHP syntax error.*test\.php.*braces/');
        
        new FirestoreRequestParamProcessor($this->classWithSyntaxError, 'test.php');
    }

    /**
     * Test exception when constructor receives content without PHP class.
     */
    public function testConstructorWithNoClass(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/No PHP class found.*test\.php/');
        
        new FirestoreRequestParamProcessor($this->noClassContent, 'test.php');
    }

    /**
     * Test static run method with temporary directory structure.
     */
    public function testStaticRunMethod(): void
    {
        $tmpDir = sys_get_temp_dir() . '/test-firestore-processor-' . rand();
        mkdir($tmpDir, 0777, true);
        
        try {
            // Create test files
            $firestoreFile = $tmpDir . '/FirestoreClient.php';
            $nonFirestoreFile = $tmpDir . '/RegularClient.php';
            
            file_put_contents($firestoreFile, $this->classWithTypo);
            file_put_contents($nonFirestoreFile, $this->nonFirestoreClass);
            
            // Capture output
            ob_start();
            FirestoreRequestParamProcessor::run($tmpDir);
            $output = ob_get_clean();
            
            // Verify Firestore file was processed
            $this->assertStringContainsString("Fixed database parameter issues in: {$firestoreFile}", $output);
            
            // Verify the file was actually fixed
            $fixedContent = file_get_contents($firestoreFile);
            $this->assertStringNotContainsString('$datbase', $fixedContent);
            $this->assertStringContainsString('$database', $fixedContent);
            
            // Verify non-Firestore file was not modified (should not appear in output)
            $this->assertStringNotContainsString($nonFirestoreFile, $output);
            
        } finally {
            // Clean up
            if (is_file($firestoreFile)) {
                unlink($firestoreFile);
            }
            if (is_file($nonFirestoreFile)) {
                unlink($nonFirestoreFile);
            }
            if (is_dir($tmpDir)) {
                rmdir($tmpDir);
            }
        }
    }

    /**
     * Test error handling when file cannot be read.
     */
    public function testFileReadError(): void
    {
        $tmpDir = sys_get_temp_dir() . '/test-firestore-processor-error-' . rand();
        mkdir($tmpDir, 0777, true);
        
        try {
            $testFile = $tmpDir . '/test.php';
            file_put_contents($testFile, $this->classWithTypo);
            chmod($testFile, 0000); // Remove read permissions
            
            ob_start();
            FirestoreRequestParamProcessor::run($tmpDir);
            $output = ob_get_clean();
            
            // Should contain error message
            $this->assertStringContainsString("Error processing file {$testFile}", $output);
            
        } finally {
            // Clean up
            if (is_file($testFile)) {
                chmod($testFile, 0644); // Restore permissions for deletion
                unlink($testFile);
            }
            if (is_dir($tmpDir)) {
                rmdir($tmpDir);
            }
        }
    }

    /**
     * Test complex class with multiple methods and mixed issues.
     */
    public function testComplexClassWithMultipleIssues(): void
    {
        $complexClass = <<<'EOL'
<?php

namespace Testing\Firestore;

class ComplexFirestoreClient
{
    /**
     * @param string $datbase Database name
     * @param string $collection Collection name
     */
    public function method1(string $datbase, string $collection): void
    {
        $this->db = $datbase;
        $query = "SELECT * FROM {$datbase}.{$collection}";
    }
    
    /**
     * @param array $datbase Database config
     */
    public function method2(array $datbase): void
    {
        $config = $datbase['config'] ?? null;
    }
    
    /**
     * This method has no database parameter issues
     */
    public function method3(): void
    {
        echo "No issues here";
    }
}
EOL;

        $processor = new FirestoreRequestParamProcessor($complexClass, 'complex.php');
        $processor->fixDatabaseParameterIssues();
        
        $result = $processor->getContents();
        $this->assertTrue($processor->hasChanges());
        
        // All instances of $datbase should be replaced with $database
        $this->assertStringNotContainsString('$datbase', $result);
        
        // Count occurrences of $database (should be multiple)
        $databaseCount = substr_count($result, '$database');
        $this->assertGreaterThan(3, $databaseCount); // At least in @param comments and method bodies
        
        // Verify specific fixes
        $this->assertStringContainsString('@param string $database Database name', $result);
        $this->assertStringContainsString('@param array $database Database config', $result);
        $this->assertStringContainsString('function method1(string $database', $result);
        $this->assertStringContainsString('function method2(array $database', $result);
        $this->assertStringContainsString('$this->db = $database;', $result);
        $this->assertStringContainsString('$config = $database[\'config\']', $result);
    }

    /**
     * Test that changes preserve original formatting and indentation.
     */
    public function testFormattingPreservation(): void
    {
        $indentedClass = <<<'EOL'
<?php

namespace Testing\Firestore;

class IndentedFirestoreClient
{
    /**
     * Method with specific indentation.
     * 
     * @param    string    $datbase    The database parameter
     * @param    array     $options    Additional options
     */
    public function connect(string $datbase, array $options = []): void
    {
        if ($datbase) {
            $this->connection = $datbase;
            $this->log("Connected to: " . $datbase);
        }
    }
}
EOL;

        $processor = new FirestoreRequestParamProcessor($indentedClass, 'indented.php');
        $processor->fixDatabaseParameterIssues();
        
        $result = $processor->getContents();
        $this->assertTrue($processor->hasChanges());
        
        // Verify the parameter was fixed but formatting was preserved
        $this->assertStringContainsString('@param    string    $database    The database parameter', $result);
        $this->assertStringContainsString('function connect(string $database, array $options = [])', $result);
        $this->assertStringContainsString('$this->connection = $database;', $result);
        $this->assertStringContainsString('$this->log("Connected to: " . $database);', $result);
        
        // Should not contain the typo anymore
        $this->assertStringNotContainsString('$datbase', $result);
    }
}