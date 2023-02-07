<?php

namespace Google\Generator\Utils;

use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\MethodDeclaration;
use Microsoft\PhpParser\LineCharacterPosition;
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

    private function getMethodStartBeforeMethod($insertBeforeMethodName): LineCharacterPosition
    {
        foreach ($this->classNode->getDescendantNodes() as $childNode) {
            if ($childNode instanceof MethodDeclaration) {
                if ($childNode->getName() === $insertBeforeMethodName) {
                    return $this->getLineCharacterFromStartPosition($childNode->getStartPosition());
                }
            }
        }

        throw new LogicException(
            'Provided contents does not contain method ' . $insertBeforeMethodName
        );
    }

    private function getMethodStartBeforeFirstMethod(): LineCharacterPosition
    {
        foreach ($this->classNode->getDescendantNodes() as $childNode) {
            if ($childNode instanceof MethodDeclaration) {
                return $this->getLineCharacterFromStartPosition($childNode->getStartPosition());
            }
        }

        return $this->getLineCharacterFromStartPosition(
            $this->classNode->getEndPosition()
        );
    }

    private function getLineCharacterFromStartPosition(int $startPosition): LineCharacterPosition
    {
        return PositionUtilities::getLineCharacterPositionFromPosition(
            $startPosition,
            $this->classNode->getFileContents()
        );
    }

    public function insert(string $newContent, ?string $insertBeforeMethodName = null): void
    {
        $methodStart = $insertBeforeMethodName
            ? $this->getMethodStartBeforeMethod($insertBeforeMethodName)
            : $this->getMethodStartBeforeFirstMethod();

        $lines = explode(PHP_EOL, $this->classNode->getFileContents());
        array_splice($lines, $methodStart->line, 0, $newContent);
        $contents = implode(PHP_EOL, $lines);
        $this->classNode = self::fromCode($contents);
    }

    public function getContents(): string
    {
        return $this->classNode->getFileContents();
    }
}
