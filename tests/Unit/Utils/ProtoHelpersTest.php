<?php
/*
 * Copyright 2020 Google LLC
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

namespace Google\Generator\Tests\Unit\Utils;

use Google\Generator\Collections\Map;
use PHPUnit\Framework\TestCase;
use Google\Generator\Collections\Vector;
use Google\Generator\Tests\Tools\ProtoLoader;
use Google\Generator\Utils\ProtoHelpers;
use Google\Generator\Utils\ProtoAugmenter;
use Google\Generator\Utils\ProtoCatalog;

final class ProtoHelpersTest extends TestCase
{
    public function testProtoCustomOptions(): void
    {
        $file = ProtoLoader::loadDescriptor('Utils/custom_options.proto');

        // Check custom options are loaded successfully.
        $this->assertEquals(42, ProtoHelpers::getCustomOption($file, 2000));
        $this->assertEquals('stringy', ProtoHelpers::getCustomOption($file, 2001));
        $this->assertEquals([8, 9, 10], ProtoHelpers::getCustomOptionRepeated($file, 2002)->toArray());
        $this->assertEquals(['s1', 's2'], ProtoHelpers::getCustomOptionRepeated($file, 2003)->toArray());
    }

    public function testProtoComments(): void
    {
        $file = ProtoLoader::loadDescriptor('Utils/comments.proto');
        ProtoAugmenter::Augment(Vector::new([$file]));

        // Check comments are merged from all proto structures.
        $svc = $file->getService()[0];
        $this->assertEquals(['Svc 1', 'Svc 2'], $svc->leadingComments->toArray());
        $method = $svc->getMethod()[0];
        $this->assertEquals(['Method 1', 'Method 2'], $method->leadingComments->toArray());
        $msg = $file->getMessageType()[0];
        $this->assertEquals(['Msg 1', 'Msg 2'], $msg->leadingComments->toArray());
        $msgField = $msg->getField()[0];
        $this->assertEquals(['Field 1', 'Field 2'], $msgField->leadingComments->toArray());
        $inner = $msg->getNestedType()[0];
        $this->assertEquals(['Inner 1', 'Inner 2'], $inner->leadingComments->toArray());
        $innerField = $inner->getField()[0];
        $this->assertEquals(['Inner field 1', 'Inner field 2'], $innerField->leadingComments->toArray());
    }

    public function testProtoCatalog(): void
    {
        $file = ProtoLoader::loadDescriptor('Utils/catalog.proto');
        $files = Vector::new([$file]);
        // ProtoCatalog depends on the FileDescriptorProtos already being augmented.
        ProtoAugmenter::Augment($files);
        $catalog = new ProtoCatalog($files);

        $msg = $catalog->msgsByFullname['.foo.Msg'];
        $this->assertEquals('Msg', $msg->GetName());
        $msg = $catalog->msgsByFullname['.foo.Msg.InnerMsg'];
        $this->assertEquals('InnerMsg', $msg->GetName());
        $enm = $catalog->enumsByFullname['.foo.Enm'];
        $this->assertEquals('Enm', $enm->getName());
        $enm = $catalog->enumsByFullname['.foo.Msg.InnerEnm'];
        $this->assertEquals('InnerEnm', $enm->getName());
        $svc = $catalog->servicesByFullname['.foo.Svc'];
        $this->assertEquals('Svc', $svc->GetName());
        $f = $catalog->filesByService[$svc];
        $this->assertStringContainsString('catalog.proto', $f->GetName());
    }

    public function testRoutingParamsDescriptor(): void
    {
        $routingParams = [
            'foo' => Vector::new([
                [
                    'getter' => ['getFoo'],
                    'key' => 'foo',
                ],
                [
                    'getter' => ['getFoo'],
                    'key' => 'foo_name',
                ],
                [
                    'getter' => ['getFoo'],
                    'key' => 'foo',
                    'regex' => '/^(?<foo>projects\/[^\/]+)\/bars\/[^\/]+(?:\/.*)?$/'
                ]
            ]),
            'bar.baz' => Vector::new([
                [
                    'getter' => ['getBar', 'getBaz'],
                    'key' => 'baz',
                    'regex' => '/^(?<baz>projects\/[^\/]+)\/bars\/[^\/]+(?:\/.*)?$/'
                ],
                [
                    'getter' => ['getBar', 'getBaz'],
                    'key' => 'bar_baz',
                ],
                [
                    'getter' => ['getBar', 'getBaz'],
                    'key' => 'baz',
                    'regex' => '/^(?<bar>projects\/[^\/]+)\/bars\/[^\/]+(?:\/.*)?$/'
                ]
            ])
        ];
        $expected = [
            [
                'fieldAccessors' => ['getFoo'],
                'keyName' => 'foo',
                'matchers' => ['/^(?<foo>projects\/[^\/]+)\/bars\/[^\/]+(?:\/.*)?$/']
            ],
            [
                'fieldAccessors' => ['getFoo'],
                'keyName' => 'foo_name'
            ],
            [
                'fieldAccessors' => ['getBar', 'getBaz'],
                'keyName' => 'baz',
                'matchers' => ['/^(?<baz>projects\/[^\/]+)\/bars\/[^\/]+(?:\/.*)?$/', '/^(?<bar>projects\/[^\/]+)\/bars\/[^\/]+(?:\/.*)?$/']
            ],
            [
                'fieldAccessors' => ['getBar', 'getBaz'],
                'keyName' => 'bar_baz'
            ]
        ];
        $actual = ProtoHelpers::routingParamsDescriptor(Map::new($routingParams));
        $this->assertEquals($expected, $actual);
    }

    public function testImplicitParamsDescriptor(): void
    {
        $implicitParams = [
            'foo' => Vector::new(['getFoo']),
            'bar.baz' => Vector::new(['getBar', 'getBaz'])
        ];
        $expected = [
            [
                'fieldAccessors' => ['getFoo'],
                'keyName' => 'foo',
            ],
            [
                'fieldAccessors' => ['getBar', 'getBaz'],
                'keyName' => 'bar.baz',
            ]
        ];
        $actual = ProtoHelpers::implicitParamsDescriptor(Map::new($implicitParams));
        $this->assertEquals($expected, $actual);
    }
}
