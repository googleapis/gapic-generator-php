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

namespace Google\Generator\Ast;

trait HasPhpDoc
{
    /**
     * Create a version of this ast element with PHP doc.
     *
     * @param PhpDoc $phpDoc The PHP doc to use.
     *
     * @return self
     */
    public function withPhpDoc(PhpDoc $phpDoc): self
    {
        return $this->clone(fn ($clone) => $clone->phpDoc = $phpDoc);
    }

    /**
     * Create a version of this ast element with PHP doc.
     *
     * @param string $summaryText The summary text to use as a PHP doc.
     *
     * @return self
     */
    public function WithPhpDocText(string $summaryText): self
    {
        return $this->withPhpDoc(PhpDoc::block(PhpDoc::text($summaryText)));
    }

    protected function phpDocToCode(): string
    {
        return isset($this->phpDoc) ? $this->phpDoc->toCode() : '';
    }
}
