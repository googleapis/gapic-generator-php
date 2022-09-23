<?php

namespace Google\Generator\Utils;

use ast;

class CodeReplaceMethod
{
    private const VERSION=70;

    private ast\Node $node;

    public function __construct(private string $contents, string $methodName = null)
    {
        $this->node = self::fromCode($contents, $methodName);
    }

    private static function fromCode(string $contents, string $methodName = null)
    {
        $ast = ast\parse_code($contents, self::VERSION);

        foreach ($ast->children as $fileChild) {
            if ($fileChild->kind === ast\AST_CLASS) {
                foreach ($fileChild->children['stmts']->children as $classChild) {
                    if ($classChild->kind === ast\AST_METHOD) {
                        // Use the first method if no method is specified
                        if (is_null($methodName) || $classChild->children['name'] === $methodName) {
                            return $classChild;
                        }
                    }
                }
            }
        }

        throw $methodName
            ? new \Exception('Could not find method ' . $methodnName . ' in file')
            : new \Exception('No methods in file');
    }

    public function getMethodContents(): string
    {
        $startIndex = $this->getLineNumber() - 1;
        $length = $this->getEndLineNumber() - $startIndex;

        $contents = array_slice(explode(PHP_EOL, $this->contents), $startIndex, $length);

        return implode(PHP_EOL, $contents);
    }

    public function insertBefore(string $newContent): void
    {
        $lines = explode(PHP_EOL, $this->contents);
        array_splice($lines, $this->getLineNumber() - 1, 0, $newContent);
        $this->contents = implode(PHP_EOL, $lines);
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    private function getLineNumber(): int
    {
        $lineNo = $this->node->lineno;
        if (!empty($this->node->children['docComment'])) {
            $commentLines = substr_count($this->node->children['docComment'], "\n") + 1;
            $lineNo -= $commentLines;
        }
        return $lineNo;
    }

    private function getEndLineNumber(): int
    {
        return $this->node->endLineno;
    }
}
