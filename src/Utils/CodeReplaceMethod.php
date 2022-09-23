<?php

namespace Google\Generator\Utils;

use Microsoft\PhpParser\Node\MethodDeclaration;
use Microsoft\PhpParser\Parser;
use Microsoft\PhpParser\PositionUtilities;
use LogicException;
use ParseError;

class CodeReplaceMethod
{
    private MethodDeclaration $method;

    public function __construct(string $contents, private ?string $methodName = null)
    {
        $this->method = self::fromCode($contents, $methodName);
    }

    private static function fromCode(string $contents, string $methodName = null)
    {
        $parser = new Parser();
        $astNode = $parser->parseSourceFile($contents);

        foreach ($astNode->getDescendantNodes() as $childNode) {
            if ($childNode instanceof MethodDeclaration) {
                if (!$methodName || $childNode->getName() === $methodName) {
                    return $childNode;
                }
            }
        }

        throw $methodName
            ? new LogicException('Could not find method ' . $methodName . ' in file')
            : new LogicException('No methods in file');
    }

    public function insertBefore(string $newContent): void
    {
        $contents = $this->method->getFileContents();
        $methodStart = PositionUtilities::getLineCharacterPositionFromPosition(
            $this->method->getStartPosition(),
            $contents
        );
        $lines = explode(PHP_EOL, $contents);
        array_splice($lines, $methodStart->line, 0, $newContent);
        $contents = implode(PHP_EOL, $lines);
        try {
            $this->method = self::fromCode($contents);
        } catch (LogicException $e) {
            throw new ParseError('Insertion caused a syntax error');
        }
    }

    public function getContents(): string
    {
        return $this->method->getFileContents();
    }
}
