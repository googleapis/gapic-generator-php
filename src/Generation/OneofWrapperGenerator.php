<?php
/*
 * Copyright 2021 Google LLC
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

namespace Google\Generator\Generation;

use Google\Generator\Ast\Access;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\PhpClass;
use Google\Generator\Ast\PhpClassMember;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Collections\Map;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\CustomOptions;
use Google\Generator\Utils\Helpers;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\Type;
use Google\Protobuf\Internal\FieldDescriptor;
use Google\Protobuf\Internal\OneofDescriptor;

class OneofWrapperGenerator
{
    /**
     * Returns a vector of PhpFiles for each required oneof field that is used
     * in a method signature.
     */
    public static function generate(SourceFileContext $ctx, ServiceDetails $serviceDetails): Vector
    {
        return (new OneOfWrapperGenerator($ctx, $serviceDetails))->generateImpl();
    }

    private SourceFileContext $ctx;
    private ServiceDetails $serviceDetails;

    private function __construct(SourceFileContext $ctx, ServiceDetails $serviceDetails)
    {
        $this->ctx = $ctx;
        $this->serviceDetails = $serviceDetails;
    }

    /** Returns a vector of PhpFiles. **/
    private function generateImpl(): Vector
    {
        // Get a set of required oneofs from input message fields.
        // Use a vector instead of a set, since adding contains() to Set would have a similar
        // performance cost not lower than calling Vector::contains() directly.
        $oneofs = Map::new([]);
        $oneofContainingMessageNames = Vector::new([]);
        foreach ($this->serviceDetails->methods as $method) {
            $requiredFieldNames = $method->requiredFields->map(fn ($x) => $x->name);
            $currOneofIndex = -1;
            $currOneofFieldNames = Vector::new([]) ;
            foreach ($method->requiredFields as $requiredField) {
                if (!$requiredField->isOneOf
                    || ($requiredField->oneOfIndex === $currOneofIndex && $currOneofFieldNames->contains($requiredField->name))) {
                    continue;
                }

                $containingMessage = $requiredField->containingMessage;
                // empty() doesn't work here.  ¯\_(ツ)_/¯
                if (sizeof($containingMessage->getOneofDecl()) === 0) {
                    continue;
                }

                // Hazard zone: If the parsing logic in this codebase continues to work as intended,
                // then the containing message's required fields should match up with those in $method.
                // Ideally we'd use $method->requiredFields to construct the oneof descriptor, but any
                // discrepancy could result in a field list mismatch against the containing message's
                // fields in the oneof, when we generate the wrapper class. Either way, there will
                // be error-prone risks, so we choose to align the wrapper class with the containing
                // message's definition.
                $currOneofIndex = $requiredField->oneOfIndex;
                $currOneofFieldNames = Vector::new([]);
                // Vector of FieldDescriptors.
                $currOneofFieldDescs = Vector::new([]);
                // OneofDescriptorProto.
                $oneofDescProto = $containingMessage->getOneofDecl()[$currOneofIndex];

                foreach ($containingMessage->getField() as $containingMessageFieldDescProto) {
                    $isOneof = $containingMessageFieldDescProto->hasOneofIndex();
                    if (!$isOneof || $containingMessageFieldDescProto->getOneofIndex() !== $currOneofIndex) {
                        continue;
                    }
                    $isRequired =
                        ProtoHelpers::getCustomOptionRepeated($containingMessageFieldDescProto, CustomOptions::GOOGLE_API_FIELDBEHAVIOR)
                            ->contains(CustomOptions::GOOGLE_API_FIELDBEHAVIOR_REQUIRED);
                    // Either all fields in a oneof must be optional or required. Mixing these
                    // is weird and wrong by current (2021/07) AIP standards.
                    if (!$isRequired) {
                        throw new \Exception('Either all fields, or none, in ' . $containingMessage->getName()
                            . ' containing oneof ' .  $oneofDescProto->getName() . ' must be marked as required, but '
                            . $containingMessageFieldDescProto->getName() . ' was not');
                    }
                    // Assert that this field appears in the method's required fields.
                    if (!$requiredFieldNames->contains($containingMessageFieldDescProto->getName())
                        || $requiredField->oneOfIndex !== $containingMessageFieldDescProto->getOneofIndex()) {
                        throw new \Exception('Field ' . $containingMessageFieldDescProto->getName()
                            . ' found in containing message ' . $containingMessage->getName()
                            . ' but not in the list of required fields for method ' . $method);
                    }
                    $currOneofFieldNames = $currOneofFieldNames->append($containingMessageFieldDescProto->getName());
                    $currOneofFieldDescs = $currOneofFieldDescs->append(FieldDescriptor::getFieldDescriptor($containingMessageFieldDescProto));
                }

                $oneof = new OneofDescriptor();
                $oneof->setName($oneofDescProto->getName());
                foreach ($currOneofFieldDescs as $fieldDesc) {
                    $oneof->addField($fieldDesc);
                }
                $oneofs = $oneofs->set($oneof, $containingMessage->getName());
            }
        }

        // empty() also doesn't work here.  ¯\_(ツ)_/¯
        if ($oneofs->count() === 0) {
            return Vector::new([]);
        }

        $classes = $oneofs->keys()->map(fn ($x) => $this->generateClass($x, $oneofs->get($x, null)));
        $files = $classes->map(fn ($c) =>
            AST::file($c)
                ->withApacheLicense($this->ctx->licenseYear)
                ->withGeneratedFromProtoCodeWarning($this->serviceDetails->filePath, $this->serviceDetails->isGa()));
        return $files->map(fn ($f) => $this->ctx->finalize($f));
    }

    /**
     * Generates a wrapper class for a oneof.
     *
     * @param $oneofDesc OneofDescriptor (https://github.com/protocolbuffers/protobuf/blob/21b0e5587c01948927ede9be789671ff116b7ad4/php/src/Google/Protobuf/Internal/OneofDescriptor.php).
     */
    public function generateClass(OneofDescriptor $oneofDesc, string $containingMessageName): PhpClass
    {
        // Set the wrapper class type.
        // Keep this in sync with the logic in FieldDetails::toOneofWrapperType.
        $oneofCamelName = Helpers::toUpperCamelCase($oneofDesc->getName());
        $oneofWrapperClassName = "{$oneofCamelName}Oneof";
        $namespace = $this->serviceDetails->namespace . "\\$containingMessageName";
        $generatedOneofWrapperType = Type::fromName("$namespace\\$oneofWrapperClassName");

        // TODO(miraleung): Add PhpDoc and methods.
        return AST::class($generatedOneofWrapperType)
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::preFormattedText($this->serviceDetails->docLines->skip(1)
                    ->prepend("Wrapper class for the oneof {$oneofDesc->getName()} defined in message {$oneofDesc->getFields()[0]->getMessageType()}"))
            ))
            ->withMembers($this->fieldProperties($oneofDesc))
            ->withMember($this->selectedFieldProperty())
            ->withMembers(Vector::new($oneofDesc->getFields())->map(fn ($f) => $this->setterMethod($f, $generatedOneofWrapperType)))
            ->withMembers(Vector::new($oneofDesc->getFields())->map(fn ($f) => $this->isOneofTypeMethod($f)))
            ->withMembers(Vector::new($oneofDesc->getFields())->map(fn ($f) => $this->getterMethod($f)));
    }

    private function fieldProperties(OneofDescriptor $oneofDesc): Vector
    {
        // TODO(v2): Associate the fields' respective types to the properties (and add
        // the corresponding imports) when we support PHP 7.4+.
        return Vector::new($oneofDesc->getFields())
            ->map(fn ($fieldDesc) =>
                AST::property(self::getPhpFieldName($fieldDesc))
                    ->withAccess(Access::PRIVATE)
                    ->withPhpDocText('The value for the field ' . $fieldDesc->getName() . ', if set.'));
    }

    private function selectedFieldProperty(): PhpClassMember
    {
        return AST::property('selectedOneofFieldName')
            // TODO(v2): Make this a string type when we support PHP 7.4+.
            ->withAccess(Access::PRIVATE)
            ->withPhpDocText('Name of the field for which the oneof is set, as it appears in the protobuf in lower_camel_case.')
            ->withValue(AST::literal('\'\''));
    }

    private function setterMethod(FieldDescriptor $fieldDesc, Type $oneofWrapperType): PhpClassMember
    {
        $newValueFormattedName = self::getPhpFieldName($fieldDesc);
        $newValueVar = AST::var($newValueFormattedName);
        $newValueParam = AST::param(null, $newValueVar);
        return AST::method("set" . Helpers::toUpperCamelCase($fieldDesc->getName()))
            ->withAccess(Access::PUBLIC)
            ->withParams($newValueParam)
            ->withBody(AST::block(
                AST::assign(AST::access(AST::THIS, AST::property($newValueFormattedName)), $newValueVar),
                AST::assign(
                    AST::access(AST::THIS, AST::property('selectedOneofFieldName')),
                    AST::literal("'" . $fieldDesc->getName() . "'")
                ),
                // AST::THIS gives some weird errors, so use the value directly.
                AST::return(AST::literal('$this'))
            ))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::text('Sets this oneof to ' . $fieldDesc->getName() . ' and updates its value.'),
                PhpDoc::param(
                    $newValueParam,
                    PhpDoc::text('The new value of this oneof.'),
                    $this->ctx->type(Type::fromField($this->serviceDetails->catalog, $fieldDesc))
                ),
                PhpDoc::return($this->ctx->type($oneofWrapperType), PhpDoc::text('The modified object'))
            ));
    }

    private function isOneofTypeMethod(FieldDescriptor $fieldDesc): PhpClassMember
    {
        $newValueFormattedName = self::getPhpFieldName($fieldDesc);
        $newValueVar = AST::var($newValueFormattedName);
        $newValueParam = AST::param(null, $newValueVar);
        return AST::method("is" . Helpers::toUpperCamelCase($fieldDesc->getName()))
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                AST::return(AST::binaryOp(
                    AST::access(AST::THIS, AST::property('selectedOneofFieldName')),
                    '===',
                    AST::literal("'" . $fieldDesc->getName() . "'")
                ))
            ))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::text('Returns true if this oneof is set to the field ' .$fieldDesc->getName() . '.'),
                PhpDoc::return($this->ctx->type(Type::bool())),
            ));
    }

    private function getterMethod(FieldDescriptor $fieldDesc): PhpClassMember
    {
        $newValueFormattedName = self::getPhpFieldName($fieldDesc);
        $newValueVar = AST::var($newValueFormattedName);
        $newValueParam = AST::param(null, $newValueVar);
        $fieldNameUpperCamel = Helpers::toUpperCamelCase($fieldDesc->getName());
        return AST::method("get" . $fieldNameUpperCamel)
            ->withAccess(Access::PUBLIC)
            ->withBody(AST::block(
                AST::return(AST::ternary(
                    AST::call(
                        AST::THIS,
                        AST::method("is" . $fieldNameUpperCamel)->withAccess(Access::PUBLIC)
                    )(),
                    AST::access(AST::THIS, AST::property($newValueFormattedName)),
                    AST::literal('null')
                ))
            ))
            ->withPhpDoc(
                PhpDoc::block(
                PhpDoc::text('Returns $this->' . $newValueFormattedName . ' if this oneof is set to the field '
                . $fieldDesc->getName() . ', null otherwise.'),
                PhpDoc::return($this->ctx->type(Type::fromField($this->serviceDetails->catalog, $fieldDesc))),
            ));
    }

    private static function getPhpFieldName(FieldDescriptor $fieldDesc): string
    {
        return Helpers::toCamelCase($fieldDesc->getName());
    }
}
