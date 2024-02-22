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

use Google\Auth\Credentials\InsecureCredentials;
use Google\Generator\Ast\AST;
use Google\Generator\Ast\Access;
use Google\Generator\Ast\PhpDoc;
use Google\Generator\Utils\Type;
use \Grpc\ChannelCredentials;

class EmulatorSupportGenerator
{
    /**
     * Map of emulator support required clients and their expected env variables.
     */
    private static $emulatorSupportFixes = [
        '\Google\Cloud\Spanner\Admin\Database\V1\Client\DatabaseAdminClient' => 'SPANNER_EMULATOR_HOST',
        '\Google\Cloud\Spanner\Admin\Instance\V1\Client\InstanceAdminClient' => 'SPANNER_EMULATOR_HOST',
        // Added for unittesting
        '\Testing\Basic\Client\BasicClient' => 'BASIC_EMULATOR_HOST'
    ];

    public static function generateEmulatorSupportIfRequired(ServiceDetails $serviceDetails, SourceFileContext $ctx) {
        $fullClassName = $serviceDetails->gapicClientV2Type->getFullName();
        $emulatorHostVar = AST::var('emulatorHost');
        $phpUrlSchemeConst = AST::constant('PHP_URL_SCHEME');
        $schemeVar = AST::var('scheme');
        $searchVar = AST::var('search');
        $argsVar = AST::var('args');

        if (array_key_exists($fullClassName, self::$emulatorSupportFixes)) {
            $ctx->type(Type::fromName(InsecureCredentials::class));
            $ctx->type(Type::fromName(ChannelCredentials::class));
            return AST::method('setEmulatorConfig')
            ->withAccess(Access::PRIVATE)
            ->withBody(AST::block(
                AST::assign($emulatorHostVar, AST::call(AST::GET_ENV)(self::$emulatorSupportFixes[$fullClassName])),
                AST::if(AST::not(AST::call(AST::EMPTY)($emulatorHostVar)))->then(
                AST::if(AST::binaryOp(AST::call(AST::PARSE_URL)($emulatorHostVar, $phpUrlSchemeConst), '===', $schemeVar))
                    ->then(AST::block(
                        AST::assign($searchVar, AST::binaryOp($schemeVar, '.', '://')),
                        AST::assign($emulatorHostVar, AST::call(AST::STRING_REPLACE)($searchVar, '', $emulatorHostVar)),
                    )),
                AST::return(
                    AST::array([
                        'apiEndpoint' => $emulatorHostVar,
                        'transportConfig' => [
                            'grpc' => [
                                'stubOpts' => [
                                    'credentials' => AST::staticCall(
                                        $ctx->type((Type::fromName("Grpc\ChannelCredentials"))),
                                        AST::CREATE_INSECURE)()
                                ]
                            ]
                        ],
                        'credentials' => AST::new($ctx->type((Type::fromName("Google\Auth\Credentials\InsecureCredentials"))))(),
                    ])))))
            ->withPhpDoc(PhpDoc::block(
                PhpDoc::text("Configure the gapic configuration to use a service emulator."),
            ));
        } else {
            return null;
        }
    }

    public static function generateEmulatorSupportFunctionCallIfRequired(ServiceDetails $serviceDetails) {
        $setEmulatorConfig = AST::method('setEmulatorConfig');
        $options = AST::var('options');
        return array_key_exists($serviceDetails->gapicClientV2Type->getFullName(), self::$emulatorSupportFixes) ?
                Ast::assign($options, Ast::call(AST::THIS, $setEmulatorConfig)($options)) : null;
    }
}
