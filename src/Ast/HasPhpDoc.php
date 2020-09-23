<?php
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
        return $this->clone(fn($clone) => $clone->phpDoc = $phpDoc);
    }

    protected function phpDocToCode(): string
    {
        return $this->phpDoc->toCode();
    }
}
