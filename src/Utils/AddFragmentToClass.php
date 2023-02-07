<?php

namespace Google\Generator\Utils;

use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\MethodDeclaration;
use Microsoft\PhpParser\Parser;
use Microsoft\PhpParser\PositionUtilities;
use Microsoft\PhpParser\DiagnosticsProvider;
use LogicException;
use ParseError;

class AddFragmentToClass
{
    private ClassDeclaration $classNode;

    public function __construct(string $contents)
    {
        $this->classNode = self::fromCode($contents);
    }

    private static function fromCode(string $contents): ClassDeclaration
    {
        $parser = new Parser();
        $astNode = $parser->parseSourceFile($contents);
        if ($errors = DiagnosticsProvider::getDiagnostics($astNode)) {
            throw new ParseError('Provided contents contains a PHP syntax error');
        }

        foreach ($astNode->getDescendantNodes() as $childNode) {
            if ($childNode instanceof ClassDeclaration) {
                return $childNode;
            }
        }

        throw new LogicException('Provided contents does not contain a PHP class');
    }

    private function getInsertLineBeforeMethod($insertBeforeMethodName): int
    {
        foreach ($this->classNode->getDescendantNodes() as $childNode) {
            if ($childNode instanceof MethodDeclaration) {
                if ($childNode->getName() === $insertBeforeMethodName) {
                    return $this->getLineNumberFromPosition($childNode->getFullStartPosition()) + 1;
                }
            }
        }

        throw new LogicException(
            'Provided contents does not contain method ' . $insertBeforeMethodName
        );
    }

    private function getInsertLineBeforeFirstMethod(): int
    {
        foreach ($this->classNode->getDescendantNodes() as $childNode) {
            if ($childNode instanceof MethodDeclaration) {
                return $this->getLineNumberFromPosition($childNode->getFullStartPosition()) + 1;
            }
        }
        // if there are no methods in the file, insert fragment before the end of the class
        return $this->getLineNumberFromPosition($this->classNode->getEndPosition());
    }

    private function getLineNumberFromPosition(int $startPosition): int
    {
        return PositionUtilities::getLineCharacterPositionFromPosition(
            $startPosition,
            $this->classNode->getFileContents()
        )->line;
    }

    public function insert(string $newContent, ?string $insertBeforeMethodName = null): void
    {
        $insertLine = $insertBeforeMethodName
            ? $this->getInsertLineBeforeMethod($insertBeforeMethodName)
            : $this->getInsertLineBeforeFirstMethod();

        $lines = explode(PHP_EOL, $this->classNode->getFileContents());
        array_splice($lines, $insertLine, 0, $newContent);
        $contents = implode(PHP_EOL, $lines);
        $this->classNode = self::fromCode($contents);
    }

    public function getContents(): string
    {
        return $this->classNode->getFileContents();
    }
}
