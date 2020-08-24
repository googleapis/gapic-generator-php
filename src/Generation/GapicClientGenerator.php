<?php declare(strict_types=1);

namespace Google\Generator\Generation;

use \Nette\PhpGenerator\PhpFile;
use \Nette\PhpGenerator\PsrPrinter;

class GapicClientGenerator
{
    public static function Generate(SourceFileContext $ctx, ServiceDetails $serviceDetails)
    {
        return (new GapicClientGenerator($ctx, $serviceDetails))->GenerateGapicClient();
    }

    private $ctx;
    private $serviceDetails;

    private function __construct(SourceFileContext $ctx, ServiceDetails $serviceDetails)
    {
        $this->ctx = $ctx;
        $this->serviceDetails = $serviceDetails;
    }

    private function GenerateGapicClient()
    {
        $file = new PhpFile();
        $file->setStrictTypes();
        $namespace = $file->addNamespace($this->serviceDetails->clientNamespace);
        $this->ctx->SetNamespace($namespace->getName());

        // TODO: Create content.

        return (new PsrPrinter())->printFile($file);
    }
}
