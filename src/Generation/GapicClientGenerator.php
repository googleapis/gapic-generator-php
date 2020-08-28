<?php declare(strict_types=1);

namespace Google\Generator\Generation;

use Google\Generator\Ast\PhpDoc;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\ClassTypeProxy;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Constant;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;

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
        $this->ctx->setNamespace($namespace->getName());
        // Create the GAPIC client class content
        $namespace->add($this->GenerateClass($namespace));
        // Add 'use' statements at top of file, and return string of file content.
        $this->ctx->addUses($namespace);
        return (new PsrPrinter())->printFile($file);
    }

    private function GenerateClass(PhpNamespace $namespace): ClassType
    {
        $class = new ClassTypeProxy($this->serviceDetails->gapicClientClassName);
        $class->setComment(PhpDoc::block(
            PhpDoc::preFormattedText($this->serviceDetails->docLines->take(1)
                ->map(fn($x) => "Service Description: {$x}")
                ->concat($this->serviceDetails->docLines->skip(1))),
            PhpDoc::preFormattedText(Vector::new([
                'This class provides the ability to make remote calls to the backing service through method',
                'calls that map to API methods. Sample code to get started:'
            ])),
            // TODO: Include code example here.
            PhpDoc::experimental(),
        )->toCode());
        // TODO: Adding this trait is incorrectly prefixing the trait name with a '\'.
        // Find out why this is, and fix it.
        $class->addTrait(strval($this->ctx->type(Type::fromName(\Google\ApiCore\GapicClientTrait::class))));
        $class->addMember($this->serviceName());

        // TODO: Generate further class content.

        return $class->getValue();
    }

    private function serviceName(): Constant
    {
        return (new Constant('SERVICE_NAME'))
            ->setValue($this->serviceDetails->serviceName)
            ->setComment('The name of the service.');
    }
}
