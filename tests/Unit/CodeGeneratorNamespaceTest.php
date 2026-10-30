<?php
/*
 * Copyright 2026 Google LLC
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

namespace Google\Generator\Tests\Unit;

use Exception;
use Google\Generator\CodeGenerator;
use Google\Generator\Collections\Vector;
use Google\Protobuf\Internal\FileDescriptorProto;
use Google\Protobuf\Internal\FileOptions;
use Google\Protobuf\Internal\SourceCodeInfo;
use PHPUnit\Framework\TestCase;

final class CodeGeneratorNamespaceTest extends TestCase
{
    public function testMismatchedPhpNamespacesIncludeDetailsInError(): void
    {
        $fileA = $this->fileDesc('tests/Unit/a.proto', 'testing.ns_mismatch', 'Testing\\NsMismatchA');
        $fileB = $this->fileDesc('tests/Unit/b.proto', 'testing.ns_mismatch', 'Testing\\NsMismatchB');

        try {
            iterator_to_array(CodeGenerator::generate(
                Vector::new([$fileA, $fileB]),
                Vector::new([$fileA->getName(), $fileB->getName()]),
                null,
                false,
                null,
                null,
                null,
            ));
            $this->fail('Expected Exception was not thrown');
        } catch (Exception $e) {
            $this->assertStringContainsString(
                'All files in the same package must have the same PHP namespace',
                $e->getMessage()
            );
            $this->assertStringContainsString('package "testing.ns_mismatch"', $e->getMessage());
            $this->assertStringContainsString('Testing\\NsMismatchA', $e->getMessage());
            $this->assertStringContainsString('Testing\\NsMismatchB', $e->getMessage());
            $this->assertStringContainsString('tests/Unit/a.proto', $e->getMessage());
            $this->assertStringContainsString('tests/Unit/b.proto', $e->getMessage());
        }
    }

    private function fileDesc(string $name, string $package, string $phpNamespace): FileDescriptorProto
    {
        $opts = new FileOptions();
        $opts->setPhpNamespace($phpNamespace);

        $file = new FileDescriptorProto();
        $file->setName($name);
        $file->setPackage($package);
        $file->setSyntax('proto3');
        $file->setOptions($opts);
        $file->setSourceCodeInfo(new SourceCodeInfo());
        return $file;
    }
}
