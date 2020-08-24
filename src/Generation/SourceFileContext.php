<?php declare(strict_types=1);

namespace Google\Generator\Generation;

use \Google\Generator\Collections\Set;
use \Nette\PhpGenerator\PhpNamespace;

class SourceFileContext
{
    private string $namespace;

    public function __construct()
    {
        $this->namespace = '';
    }

    public function SetNamespace(string $namespace)
    {
        $this->namespace = $namespace;
    }

    // TODO: Maintain per-file data (e.g. type references).
}
