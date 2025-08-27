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

namespace Google\PostProcessor;

use LogicException;
use ParseError;

/**
 * Post-processor that fixes Firestore database parameter naming issues.
 * 
 * This processor addresses the following issues:
 * - Fixes typo: changes '$datbase' to '$database' in PHPDoc and method parameters
 * - Ensures idempotent operation (multiple runs don't duplicate changes)
 * - Uses string manipulation with validation for reliable code changes
 */
class FirestoreRequestParamProcessor implements ProcessorInterface
{
    // Magic strings extracted as constants
    private const TYPO_VARIABLE_NAME = 'datbase';
    private const CORRECT_VARIABLE_NAME = 'database';
    private const PHPDOC_PARAM_PATTERN = '/(@param\s+[^\s]+\s+)\$' . self::TYPO_VARIABLE_NAME . '\b/';
    private const PHPDOC_REPLACEMENT = '${1}$' . self::CORRECT_VARIABLE_NAME;
    private const METHOD_PARAM_PATTERN = '/\$' . self::TYPO_VARIABLE_NAME . '\b/';
    private const METHOD_PARAM_REPLACEMENT = '$' . self::CORRECT_VARIABLE_NAME;
    private const CLASS_VALIDATION_PATTERN = '/class\s+\w+/';
    
    private string $contents;
    private string $filename;
    private bool $hasChanges = false;

    /**
     * Run the processor on the input directory to find and fix Firestore files.
     *
     * @param string $inputDir The directory containing generated PHP files
     */
    public static function run(string $inputDir): void
    {
        $iterator = new \RecursiveDirectoryIterator($inputDir);
        $recursiveIterator = new \RecursiveIteratorIterator($iterator);
        $phpFiles = new \RegexIterator($recursiveIterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);
        
        foreach ($phpFiles as $finding) {
            $filePath = $finding[0];
            
            // Only process files that might contain Firestore-related code
            if (self::isFirestoreRelatedFile($filePath)) {
                self::processFile($filePath);
            }
        }
    }

    /**
     * Check if a file is related to Firestore and might need processing.
     *
     * @param string $filePath Path to the file to check
     * @return bool True if the file should be processed
     */
    private static function isFirestoreRelatedFile(string $filePath): bool
    {
        $content = file_get_contents($filePath);
        return $content !== false && 
               (strpos($content, 'firestore') !== false || 
                strpos($content, 'Firestore') !== false ||
                strpos($content, self::TYPO_VARIABLE_NAME) !== false);
    }

    /**
     * Process a single PHP file to fix database parameter issues.
     *
     * @param string $filePath Path to the file to process
     */
    private static function processFile(string $filePath): void
    {
        try {
            $content = file_get_contents($filePath);
            if ($content === false) {
                throw new LogicException("Unable to read file: {$filePath}");
            }

            $processor = new self($content, $filePath);
            $processor->fixDatabaseParameterIssues();
            
            if ($processor->hasChanges()) {
                file_put_contents($filePath, $processor->getContents());
                echo "Fixed database parameter issues in: {$filePath}\n";
            }
        } catch (\Throwable $e) {
            echo "Error processing file {$filePath}: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Create a new processor instance.
     *
     * @param string $contents PHP file contents
     * @param string $filename Filename for error reporting
     * @throws LogicException If contents don't contain a valid PHP class
     * @throws ParseError If contents contain syntax errors
     */
    public function __construct(string $contents, string $filename = 'unknown')
    {
        $this->filename = $filename;
        $this->contents = $contents;
        $this->validateContents();
    }

    /**
     * Validate PHP contents for basic syntax and class presence.
     *
     * @throws ParseError If basic syntax validation fails
     * @throws LogicException If no PHP class is found
     */
    private function validateContents(): void
    {
        // Basic syntax validation by checking for PHP opening tag and balanced braces
        if (!str_starts_with(trim($this->contents), '<?php')) {
            throw new ParseError("PHP syntax error in {$this->filename}: Missing PHP opening tag");
        }
        
        // Check for balanced braces (simple syntax check)
        $openBraces = substr_count($this->contents, '{');
        $closeBraces = substr_count($this->contents, '}');
        if ($openBraces !== $closeBraces) {
            throw new ParseError("PHP syntax error in {$this->filename}: Unbalanced braces");
        }
        
        // Ensure we have at least one class
        if (!preg_match(self::CLASS_VALIDATION_PATTERN, $this->contents)) {
            throw new LogicException("No PHP class found in {$this->filename}");
        }
    }

    /**
     * Fix database parameter issues in the loaded PHP code.
     * 
     * This method orchestrates both PHPDoc and method body fixes while
     * ensuring idempotent operation.
     */
    public function fixDatabaseParameterIssues(): void
    {
        // First pass: fix PHPDoc comments
        $this->fixPhpDocComments();
        
        // Second pass: fix method parameters and variable references
        $this->fixMethodParameters();
    }

    /**
     * Fix PHPDoc comments that contain the database parameter typo.
     * 
     * Searches for @param annotations with '$datbase' and replaces with '$database'.
     * Only makes changes if the typo is present and hasn't been fixed already.
     */
    private function fixPhpDocComments(): void
    {
        // Check if we already have the correct parameter name and incorrect one doesn't exist
        if (strpos($this->contents, '$' . self::TYPO_VARIABLE_NAME) === false) {
            return; // No typo to fix
        }
        
        // Use regex to fix PHPDoc @param annotations
        $fixedContents = preg_replace(
            self::PHPDOC_PARAM_PATTERN,
            self::PHPDOC_REPLACEMENT,
            $this->contents
        );
        
        if ($fixedContents !== $this->contents) {
            $this->contents = $fixedContents;
            $this->hasChanges = true;
        }
    }

    /**
     * Fix method parameters and variable references in method bodies.
     * 
     * Uses regex pattern matching to find and fix variable references while maintaining
     * proper PHP syntax and formatting.
     */
    private function fixMethodParameters(): void
    {
        // Check if there are still any instances of the typo
        if (strpos($this->contents, '$' . self::TYPO_VARIABLE_NAME) === false) {
            return; // No typo to fix
        }
        
        // Use regex to fix method parameters and variable references
        $fixedContents = preg_replace(
            self::METHOD_PARAM_PATTERN,
            self::METHOD_PARAM_REPLACEMENT,
            $this->contents
        );
        
        if ($fixedContents !== $this->contents) {
            $this->contents = $fixedContents;
            $this->hasChanges = true;
        }
    }

    /**
     * Get the processed PHP contents.
     *
     * @return string The PHP code with fixes applied
     */
    public function getContents(): string
    {
        return $this->contents;
    }

    /**
     * Check if any changes were made during processing.
     *
     * @return bool True if changes were made
     */
    public function hasChanges(): bool
    {
        return $this->hasChanges;
    }
}