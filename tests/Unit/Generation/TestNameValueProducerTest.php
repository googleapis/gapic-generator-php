<?php
/*
 * Copyright 2023 Google LLC
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

namespace Google\Generator\Tests\Unit\Generation;

use PHPUnit\Framework\TestCase;
use Google\Generator\Collections\Vector;
use Google\Generator\Generation\TestNameValueProducer;
use Google\Generator\Generation\SourceFileContext;
use Google\Generator\Generation\FieldDetails;
use Google\Generator\Tests\Tools\ProtoLoader;
use Google\Generator\Utils\ProtoAugmenter;
use Google\Generator\Utils\ProtoCatalog;

final class TestNameValueProducerTest extends TestCase
{
    public function testNoMoreThan24BitsOfPrecision(): void
    {
        $file = ProtoLoader::loadDescriptor('Utils/example.proto');
        // ProtoCatalog depends on the FileDescriptorProtos already being augmented.
        $files = Vector::new([$file]);
        ProtoAugmenter::Augment($files);
        $catalog = new ProtoCatalog($files);

        // Get "analysys_percentage" field for testing
        $msgDescriptor = $catalog->msgsByFullname->get('.example.Request', null);
        $fieldDescriptor = $msgDescriptor->getField()[2];
        $fieldDetails = new FieldDetails($catalog, $msgDescriptor, $fieldDescriptor);

        // Assert the test value for the field is smaller than 2^24
        $testNameValueProducer = new TestNameValueProducer($catalog, new SourceFileContext(''));
        $val = $testNameValueProducer->value($fieldDetails);
        $this->assertLessThan(pow(2, 24), $val->toCode());
    }
}
