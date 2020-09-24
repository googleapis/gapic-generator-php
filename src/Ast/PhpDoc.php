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

use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;

abstract class PhpDoc
{
    /**
     * Create a PHP Documentation block.
     * Items passed in make up the content of the block.
     *
     * @param array $items The block contents.
     *
     * @return PhpDoc
     */
    public static function block(...$items): PhpDoc
    {
        return new class(Vector::new($items)->flatten()->filter(fn($x) => !is_null($x))) extends PhpDoc
        {
            public function __construct($items)
            {
                $this->items = $items;
                $this->isBlock = true;
            }
            protected function toLines(Map $info): Vector
            {
                $info = Map::new();
                foreach ($this->items as $item)
                {
                    $info = $item->preProcess($info);
                }
                return Vector::zip($this->items, $this->items->skip(1)->append(null))->flatMap(function($x) use($info) {
                    [$item, $next] = $x;
                    $result = $item->toLines($info);
                    if (!is_null($next) && !(isset($item->isParam) && isset($next->isParam))) {
                        $result = $result->append('');
                    }
                    return $result;
                });
            }
        };
    }

    /**
     * Create a new-line within the PHP doc content.
     *
     * @return PhpDoc
     */
    public static function newLine(): PhpDoc
    {
        return new class extends PhpDoc
        {
            protected function toLines(Map $info): Vector
            {
                return Vector::new();
            }
        };
    }

    /**
     * Create zero or more lines of pre-formatted text within a PHP doc block.
     * The lines specified are added to the PHP doc with no extra processing.
     *
     * @param Vector $lines Vector of string; the lines of content.
     *
     * @return PhpDoc
     */
    public static function preFormattedText(Vector $lines): PhpDoc
    {
        return new class($lines) extends PhpDoc
        {
            public function __construct($lines)
            {
                $this->lines = $lines;
            }
            protected function toLines(Map $info): Vector
            {
                return $this->lines;
            }
        };
    }

    /**
     * Add unformatted text to the PHP doc block.
     * The parts specified may be a variety of types, which are processed according to type.
     * Output is formatted to fit within a fixed line length (of 80).
     *
     * @param array $parts The doc parts
     *
     * @return PhpDoc
     */
    public static function text(...$parts): PhpDoc
    {
        return new class(Vector::new($parts)) extends PhpDoc
        {
            public function __construct($parts)
            {
                $this->parts = $parts;
            }
            protected function toLines(Map $info): Vector
            {
                $lineLen = 80;
                $lines = Vector::new();
                $line = '';

                $commitLine = function() use(&$lines, &$line) {
                    if ($line !== '') {
                        $lines = $lines->append($line);
                        $line = '';
                    }
                };
                $add = function($s) use(&$lines, &$line, $lineLen, $commitLine) {
                    if (strlen($line) + 1 + strlen($s) > $lineLen && $line !== '') {
                        $commitLine();
                    }
                    if ($line === '') {
                        $line = $s;
                    } else {
                        $line .= ' ' . $s;
                    }
                };

                foreach ($this->parts as $part) {
                    // TODO: Add further type-specific processing as required.
                    if (is_string($part)) {
                        $words = explode(' ', $part);
                        foreach ($words as $word) {
                            if ($word !== '') {
                                $add($word);
                            }
                        }
                    } elseif ($part instanceof PhpDoc) {
                        $commitLine();
                        foreach ($part->toLines(Map::new()) as $line) {
                            $commitLine();
                        }
                    } else {
                        throw new \Exception('Cannot convert part to text');
                    }
                }
                $commitLine();
                return $lines;
            }
        };
    }

    /**
     * Add the @experimental tag to the PHP doc block.
     *
     * @return PhpDoc
     */
    public static function experimental(): PhpDoc
    {
        return new class extends PhpDoc {
            protected function toLines(Map $info): Vector
            {
                return Vector::new(['@experimental']);
            }
        };
    }

    /** Override to provide content-specific pre-processing. */
    // Note: Currently unused; will be used when support for @param is added.
    protected function preProcess(Map $info): Map
    {
        return $info;
    }

    /** Override to convert this PhpDoc to lines of text. */
    protected abstract function toLines(Map $info): Vector;

    /**
     * Convert this PhpDoc block to lines of text suitable for directly
     * including in the output PHP file.
     *
     * @return string
     */
    public function toCode(): string
    {
        $lines = $this->toLines(Map::new());
        if (count($lines) <= 1) {
            return "/** {$lines->join()} */\n";
        } else {
            return
                "/**\n" .
                $lines->map(fn($x) => ' *' . (strlen($x) === 0 ? "\n" : " {$x}\n"))->join() .
                " */\n";
        }
    }
}
