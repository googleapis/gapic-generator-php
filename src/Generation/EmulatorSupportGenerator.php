<?php
/*
 * Copyright 2024 Google LLC
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

use Google\Generator\Ast\AST;
use Google\Generator\Ast\Access;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Utils\ResolvedType;
use Google\Generator\Utils\Type;

class EmulatorSupportGenerator
{
    /**
     * Map of emulator support required clients and their expected env variables.
     */
    private static $emulatorSupportClients = [
        '\Google\Cloud\Spanner\Admin\Database\V1\Client\DatabaseAdminClient' => 'SPANNER_EMULATOR_HOST',
        '\Google\Cloud\Spanner\Admin\Instance\V1\Client\InstanceAdminClient' => 'SPANNER_EMULATOR_HOST',
        '\Google\Cloud\Spanner\V1\Client\SpannerClient' => 'SPANNER_EMULATOR_HOST',
        '\Google\Cloud\Bigtable\Admin\V2\Client\BigtableInstanceAdminClient' => 'BIGTABLE_EMULATOR_HOST',
        '\Google\Cloud\Bigtable\Admin\V2\Client\BigtableTableAdminClient' => 'BIGTABLE_EMULATOR_HOST',
        '\Google\Cloud\Bigtable\V2\Client\BigtableClient' => 'BIGTABLE_EMULATOR_HOST',
        '\Google\Cloud\PubSub\V1\Client\PublisherClient' => 'PUBSUB_EMULATOR_HOST',
        '\Google\Cloud\PubSub\V1\Client\SubscriberClient' => 'PUBSUB_EMULATOR_HOST',
        '\Google\Cloud\PubSub\V1\Client\SchemaServiceClient' => 'PUBSUB_EMULATOR_HOST',
        // Added for unittesting
        '\Testing\Basic\Client\BasicClient' => 'BASIC_EMULATOR_HOST'
    ];

    /** @var string Name of the default emulator config function. */
    public const DEFAULT_EMULATOR_CONFIG_FN = 'setDefaultEmulatorConfig';

    public static function generateEmulatorSupport(ServiceDetails $serviceDetails, SourceFileContext $ctx)
    {
        $fullClassName = $serviceDetails->gapicClientV2Type->getFullName();
        $emulatorHostVar = AST::var('emulatorHost');
        $phpUrlSchemeConst = AST::constant('PHP_URL_SCHEME');
        $schemeVar = AST::var('scheme');
        $searchVar = AST::var('search');
        $optionsVar = AST::var('options');
        $transportConfigIndexVar = AST::index(AST::index(AST::index(AST::index(
            $optionsVar,
            'transportConfig'
        ), 'grpc'), 'stubOpts'), 'credentials');

        if (!array_key_exists($fullClassName, self::$emulatorSupportClients)) {
            return null;
        }

        return AST::method(self::DEFAULT_EMULATOR_CONFIG_FN)
            ->withAccess(Access::PRIVATE)
            ->withParams(AST::param(ResolvedType::array(), $optionsVar))
            ->withBody(AST::block(
                AST::assign($emulatorHostVar, AST::call(AST::GET_ENV)(self::$emulatorSupportClients[$fullClassName])),
                AST::if(AST::call(AST::EMPTY)($emulatorHostVar))->then(AST::return($optionsVar)),
                AST::if(AST::assign($schemeVar, AST::call(AST::PARSE_URL)($emulatorHostVar, $phpUrlSchemeConst)))
                    ->then(AST::block(
                        AST::assign($searchVar, AST::binaryOp($schemeVar, '.', '://')),
                        AST::assign($emulatorHostVar, AST::call(AST::STRING_REPLACE)($searchVar, '', $emulatorHostVar)),
                    )),
                AST::nullCoalescingAssign(AST::index($optionsVar, 'apiEndpoint'), $emulatorHostVar),
                AST::if(AST::call(AST::CLASS_EXISTS)(
                    AST::access($ctx->type(Type::fromName("Grpc\ChannelCredentials")), AST::CLS)
                ))
                    ->then(AST::block(
                        AST::nullCoalescingAssign($transportConfigIndexVar, AST::staticCall(
                            $ctx->type((Type::fromName("Grpc\ChannelCredentials"))),
                            AST::method('createInsecure')
                        )()),
                    )),
                AST::nullCoalescingAssign(
                    AST::index($optionsVar,'credentials'),
                    AST::new($ctx->type((Type::fromName("Google\ApiCore\InsecureCredentialsWrapper"))))()
                ),
                AST::return($optionsVar)
            ), AST::return(AST::array([])))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::text("Configure the gapic configuration to use a service emulator.")
            ))
            ->withReturnType($ctx->type(Type::array()));
    }

    public static function generateEmulatorOptions(ServiceDetails $serviceDetails, AST $options)
    {
        $setDefaultEmulatorConfig = AST::method(self::DEFAULT_EMULATOR_CONFIG_FN);

        if (!array_key_exists($serviceDetails->gapicClientV2Type->getFullName(), self::$emulatorSupportClients)) {
            return null;
        }

        return AST::assign($options, AST::call(AST::THIS, $setDefaultEmulatorConfig)($options));
    }

    public static function generateEmulatorPhpDoc(ServiceDetails $serviceDetails)
    {
        return array_key_exists($serviceDetails->gapicClientV2Type->getFullName(), self::$emulatorSupportClients) ?
            PhpDoc::text(sprintf('Setting the "%s" environment variable will automatically set the API Endpoint to ' .
            'the value specified in the variable, as well as ensure that empty credentials are used in ' .
            'the transport layer.', self::$emulatorSupportClients[$serviceDetails->gapicClientV2Type->getFullName()])) :
            null;
    }
}
