<?php
/*
 * Copyright 2020 Google LLC
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

namespace Google\Generator\Generation;

use Google\Generator\Ast\PhpFile;
use Google\Generator\Collections\Set;
use Google\Generator\Utils\ResolvedType;
use Google\Generator\Utils\Type;

/** Track per-file data. */
class SourceFileContext
{
    private string $namespace;
    private Set $uses;
    private bool $isFinalized;

    /** @var *Readonly* Year value for license headers, if set. */
    public ?int $licenseYear;

    public function __construct(string $namespace, ?int $licenseYear = null)
    {
        $this->namespace = $namespace;
        $this->licenseYear = $licenseYear;
        $this->uses = Set::new();
        $this->isFinalized = false;
    }

    private function checkFinalized(bool $expected): void
    {
        if ($this->isFinalized !== $expected) {
            throw new \Exception($expected ?
                'This operation is only valid when the source-file-context has been finalized.' :
                'This operation is only valid when the source-file-context has not been finalized.');
        }
    }

    /**
     * The type specified is being used in this file.
     * Return the correct ResolvedType to use in the generated source code.
     *
     * @param Type $type The type being used.
     * @param mixed $fullyQualify true to fully-qualify type; +ve int to fully-qualify with chars removed.
     *
     * @return ResolvedType
     */
    public function type(Type $type, $fullyQualify = false): ResolvedType
    {
        $this->checkFinalized(false);
        // TODO(vNext): Remove `fullyQualify` support when no longer required.
        if ($fullyQualify) {
            return new ResolvedType($type, function() use($type, $fullyQualify) {
                $this->checkFinalized(true);
                return is_int($fullyQualify) ? substr($type->getFullname(), $fullyQualify) : $type->getFullname();
            });
        } else {
            // TODO: Handle type name collisions.
            if ($type->isClass()) {
                if ($type->getNamespace() !== $this->namespace) {
                    // No 'use' required if type is in the current namespace
                    $this->uses = $this->uses->add($type);
                }
            }
            return new ResolvedType($type, function() use($type) {
                $this->checkFinalized(true);
                return $type->name;
            });
        }
    }

    /**
     * Finalize this source context; after this call there must be no further changes.
     * The PhpFile passed in will be altered as necessary for this finalization.
     *
     * @param ?PhpFile $file The file being generared with this context.
     *                      This may be null when generating partial code; e.g. for code examples.
     *
     * @return ?PhpFile
     */
    public function finalize(?PhpFile $file): ?PhpFile
    {
        if (!is_null($file)) {
            $result = $file->withUses($this->uses);
        } else {
            $result = null;
        }
        $this->isFinalized = true;
        return $result;
    }
}
