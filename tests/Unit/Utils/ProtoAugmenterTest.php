<?php
/*
 * Copyright 2026 Google LLC
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

use PHPUnit\Framework\TestCase;
use Google\Generator\Collections\Vector;
use Google\Generator\Utils\ProtoAugmenter;
use Google\Protobuf\Internal\DescriptorProto;
use Google\Protobuf\Internal\FileDescriptorProto;
use Google\Protobuf\Internal\SourceCodeInfo;
use Google\Protobuf\Internal\SourceCodeInfo\Location;

final class ProtoAugmenterTest extends TestCase
{
    /**
     * Build a one-message file whose message comments come from the given leading comment
     * strings, one SourceCodeInfo location per string. descriptor.proto permits several
     * locations to share a path when a declaration is spread across multiple places.
     */
    private static function messageComments(array $leadingComments): array
    {
        $locations = [];
        foreach ($leadingComments as $comment) {
            $locations[] = new Location([
                'path' => [4, 0], // MESSAGE, index 0.
                'span' => [0, 0, 1],
                'leading_comments' => $comment,
            ]);
        }
        $file = new FileDescriptorProto([
            'name' => 'comments_multi_location.proto',
            'syntax' => 'proto3',
            'package' => 'foo',
            'message_type' => [new DescriptorProto(['name' => 'Msg'])],
            'source_code_info' => new SourceCodeInfo(['location' => $locations]),
        ]);
        ProtoAugmenter::augment(Vector::new([$file]));
        return $file->getMessageType()[0]->leadingComments->toArray();
    }

    public function testSingleLocation(): void
    {
        $this->assertEquals(['Msg 1', 'Msg 2'], static::messageComments([" Msg 1\n Msg 2\n"]));
    }

    public function testMultipleLocationsSharingAPath(): void
    {
        // The trailing blank must be dropped per location. Dropping it once over the
        // concatenation leaves every location but the last contributing a blank line.
        $this->assertEquals(
            ['First half.', 'Second half.'],
            static::messageComments([" First half.\n", " Second half.\n"])
        );
    }

    public function testMultiLineLocationsSharingAPath(): void
    {
        $this->assertEquals(
            ['A 1', 'A 2', 'B 1', 'B 2'],
            static::messageComments([" A 1\n A 2\n", " B 1\n B 2\n"])
        );
    }

    public function testCommentWithoutTrailingNewline(): void
    {
        // The blank line is only present when the comment ends in a newline, so dropping
        // the last line unconditionally eats real documentation.
        $this->assertEquals(['Line one.'], static::messageComments([' Line one.']));
        $this->assertEquals(['Line one.', 'Line two.'], static::messageComments([" Line one.\n Line two."]));
    }

    public function testBlankLinesWithinAComment(): void
    {
        // A blank line in the middle of a comment is meaningful and must survive.
        $this->assertEquals(
            ['First para.', '', 'Second para.'],
            static::messageComments([" First para.\n\n Second para.\n"])
        );
    }

    public function testNoLocations(): void
    {
        $this->assertEquals([], static::messageComments([]));
    }
}
