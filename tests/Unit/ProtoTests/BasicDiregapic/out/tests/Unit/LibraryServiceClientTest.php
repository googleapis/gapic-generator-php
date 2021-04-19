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

/*
 * GENERATED CODE WARNING
 * This file was automatically generated - do not edit!
 */

declare(strict_types=1);

namespace Testing\BasicDiregapic\Tests\Unit;

use Google\ApiCore\ApiException;

use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\LongRunning\OperationsClient;
use Google\ApiCore\Testing\GeneratedTest;

use Google\ApiCore\Testing\MockTransport;
use Google\LongRunning\GetOperationRequest;
use Google\LongRunning\Operation;

use Google\Protobuf\Any;

use Google\Protobuf\BoolValue;
use Google\Protobuf\BytesValue;

use Google\Protobuf\DoubleValue;
use Google\Protobuf\Duration;
use Google\Protobuf\FieldMask;
use Google\Protobuf\FloatValue;
use Google\Protobuf\GPBEmpty;
use Google\Protobuf\Int32Value;
use Google\Protobuf\Int64Value;
use Google\Protobuf\ListValue;
use Google\Protobuf\StringValue;
use Google\Protobuf\Struct;
use Google\Protobuf\Timestamp;
use Google\Protobuf\UInt32Value;
use Google\Protobuf\UInt64Value;
use Google\Protobuf\Value;
use Google\Rpc\Code;

use stdClass;
use Testing\BasicDiregapic\AddTagResponse;
use Testing\BasicDiregapic\ArchiveBooksResponse;
use Testing\BasicDiregapic\Book;
use Testing\BasicDiregapic\BookFromAnywhere;
use Testing\BasicDiregapic\BookFromArchive;
use Testing\BasicDiregapic\FindRelatedBooksResponse;
use Testing\BasicDiregapic\Inventory;
use Testing\BasicDiregapic\LibraryServiceClient;
use Testing\BasicDiregapic\ListAggregatedShelvesResponse;
use Testing\BasicDiregapic\ListBooksResponse;
use Testing\BasicDiregapic\ListShelvesResponse;
use Testing\BasicDiregapic\ListStringsResponse;
use Testing\BasicDiregapic\MoveBooksResponse;
use Testing\BasicDiregapic\PublishSeriesResponse;
use Testing\BasicDiregapic\SeriesUuid;
use Testing\BasicDiregapic\Shelf;
use Testing\BasicDiregapic\TestOptionalRequiredFlatteningParamsRequest\InnerEnum;
use Testing\BasicDiregapic\TestOptionalRequiredFlatteningParamsRequest\InnerMessage;
use Testing\BasicDiregapic\TestOptionalRequiredFlatteningParamsResponse;

/**
 * @group basicdiregapic
 *
 * @group gapic
 */
class LibraryServiceClientTest extends GeneratedTest
{
    /**
     * @return TransportInterface
     */
    private function createTransport($deserialize = null)
    {
        return new MockTransport($deserialize);
    }

    /**
     * @return CredentialsWrapper
     */
    private function createCredentials()
    {
        return $this->getMockBuilder(CredentialsWrapper::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @return LibraryServiceClient
     */
    private function createClient(array $options = [])
    {
        $options += [
            'credentials' => $this->createCredentials(),
        ];
        return new LibraryServiceClient($options);
    }

    /**
     * @test
     */
    public function addCommentsTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new GPBEmpty();
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $comments = [];
        $client->addComments($formattedName, $comments);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/AddComments', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $actualValue = $actualRequestObject->getComments();
        $this->assertProtobufEquals($comments, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function addCommentsExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $comments = [];
        try {
            $client->addComments($formattedName, $comments);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function addTagTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new AddTagResponse();
        $transport->addResponse($expectedResponse);
        // Mock request
        $resource = 'resource-341064690';
        $tag = 'tag114586';
        $response = $client->addTag($resource, $tag);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/AddTag', $actualFuncCall);
        $actualValue = $actualRequestObject->getResource();
        $this->assertProtobufEquals($resource, $actualValue);
        $actualValue = $actualRequestObject->getTag();
        $this->assertProtobufEquals($tag, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function addTagExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $resource = 'resource-341064690';
        $tag = 'tag114586';
        try {
            $client->addTag($resource, $tag);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function archiveBooksTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $success = false;
        $expectedResponse = new ArchiveBooksResponse();
        $expectedResponse->setSuccess($success);
        $transport->addResponse($expectedResponse);
        $response = $client->archiveBooks();
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/ArchiveBooks', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function archiveBooksExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        try {
            $client->archiveBooks();
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function createBookTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name2 = 'name2-1052831874';
        $author = 'author-1406328437';
        $title = 'title110371416';
        $read = true;
        $reader = 'reader-934979389';
        $expectedResponse = new Book();
        $expectedResponse->setName($name2);
        $expectedResponse->setAuthor($author);
        $expectedResponse->setTitle($title);
        $expectedResponse->setRead($read);
        $expectedResponse->setReader($reader);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->shelfName('[SHELF]');
        $book = new Book();
        $response = $client->createBook($formattedName, $book);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/CreateBook', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $actualValue = $actualRequestObject->getBook();
        $this->assertProtobufEquals($book, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function createBookExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->shelfName('[SHELF]');
        $book = new Book();
        try {
            $client->createBook($formattedName, $book);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function createInventoryTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name = 'name3373707';
        $expectedResponse = new Inventory();
        $expectedResponse->setName($name);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedParent = $client->publisherName('[PROJECT]', '[LOCATION]', '[PUBLISHER]');
        $formattedAsset = $client->assetName('asset-c04e34d445e31a2159c1bfeb882ba212');
        $parentAsset = 'parentAsset1389473563';
        $formattedAssets = [
            $client->assetName('assets-32bb636196f91ed59d7a49190e26b42c'),
        ];
        $response = $client->createInventory($formattedParent, $formattedAsset, $parentAsset, $formattedAssets);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/CreateInventory', $actualFuncCall);
        $actualValue = $actualRequestObject->getParent();
        $this->assertProtobufEquals($formattedParent, $actualValue);
        $actualValue = $actualRequestObject->getAsset();
        $this->assertProtobufEquals($formattedAsset, $actualValue);
        $actualValue = $actualRequestObject->getParentAsset();
        $this->assertProtobufEquals($parentAsset, $actualValue);
        $actualValue = $actualRequestObject->getAssets();
        $this->assertProtobufEquals($formattedAssets, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function createInventoryExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedParent = $client->publisherName('[PROJECT]', '[LOCATION]', '[PUBLISHER]');
        $formattedAsset = $client->assetName('asset-c04e34d445e31a2159c1bfeb882ba212');
        $parentAsset = 'parentAsset1389473563';
        $formattedAssets = [
            $client->assetName('assets-32bb636196f91ed59d7a49190e26b42c'),
        ];
        try {
            $client->createInventory($formattedParent, $formattedAsset, $parentAsset, $formattedAssets);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function createShelfTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name = 'name3373707';
        $theme = 'theme110327241';
        $internalTheme = 'internalTheme792518087';
        $expectedResponse = new Shelf();
        $expectedResponse->setName($name);
        $expectedResponse->setTheme($theme);
        $expectedResponse->setInternalTheme($internalTheme);
        $transport->addResponse($expectedResponse);
        // Mock request
        $shelf = new Shelf();
        $response = $client->createShelf($shelf);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/CreateShelf', $actualFuncCall);
        $actualValue = $actualRequestObject->getShelf();
        $this->assertProtobufEquals($shelf, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function createShelfExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $shelf = new Shelf();
        try {
            $client->createShelf($shelf);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function deleteBookTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new GPBEmpty();
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $client->deleteBook($formattedName);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/DeleteBook', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function deleteBookExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        try {
            $client->deleteBook($formattedName);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function deleteShelfTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new GPBEmpty();
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->shelfName('[SHELF]');
        $client->deleteShelf($formattedName);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/DeleteShelf', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function deleteShelfExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->shelfName('[SHELF]');
        try {
            $client->deleteShelf($formattedName);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function findRelatedBooksTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $nextPageToken = '';
        $namesElement = 'namesElement-249113339';
        $names = [
            $namesElement,
        ];
        $expectedResponse = new FindRelatedBooksResponse();
        $expectedResponse->setNextPageToken($nextPageToken);
        $expectedResponse->setNames($names);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedNames = [
            $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]'),
        ];
        $formattedShelves = [
            $client->shelfName('[SHELF]'),
        ];
        $response = $client->findRelatedBooks($formattedNames, $formattedShelves);
        $this->assertEquals($expectedResponse, $response->getPage()->getResponseObject());
        $resources = iterator_to_array($response->iterateAllElements());
        $this->assertSame(1, count($resources));
        $this->assertEquals($expectedResponse->getNames()[0], $resources[0]);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/FindRelatedBooks', $actualFuncCall);
        $actualValue = $actualRequestObject->getNames();
        $this->assertProtobufEquals($formattedNames, $actualValue);
        $actualValue = $actualRequestObject->getShelves();
        $this->assertProtobufEquals($formattedShelves, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function findRelatedBooksExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedNames = [
            $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]'),
        ];
        $formattedShelves = [
            $client->shelfName('[SHELF]'),
        ];
        try {
            $client->findRelatedBooks($formattedNames, $formattedShelves);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function getBigBookTest()
    {
        $operationsTransport = $this->createTransport();
        $operationsClient = new OperationsClient([
            'serviceAddress' => '',
            'transport' => $operationsTransport,
            'credentials' => $this->createCredentials(),
        ]);
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
            'operationsClient' => $operationsClient,
        ]);
        $this->assertTrue($transport->isExhausted());
        $this->assertTrue($operationsTransport->isExhausted());
        // Mock response
        $incompleteOperation = new Operation();
        $incompleteOperation->setName('operations/getBigBookTest');
        $incompleteOperation->setDone(false);
        $transport->addResponse($incompleteOperation);
        $name2 = 'name2-1052831874';
        $author = 'author-1406328437';
        $title = 'title110371416';
        $read = true;
        $reader = 'reader-934979389';
        $expectedResponse = new Book();
        $expectedResponse->setName($name2);
        $expectedResponse->setAuthor($author);
        $expectedResponse->setTitle($title);
        $expectedResponse->setRead($read);
        $expectedResponse->setReader($reader);
        $anyResponse = new Any();
        $anyResponse->setValue($expectedResponse->serializeToString());
        $completeOperation = new Operation();
        $completeOperation->setName('operations/getBigBookTest');
        $completeOperation->setDone(true);
        $completeOperation->setResponse($anyResponse);
        $operationsTransport->addResponse($completeOperation);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $response = $client->getBigBook($formattedName);
        $this->assertFalse($response->isDone());
        $this->assertNull($response->getResult());
        $apiRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($apiRequests));
        $operationsRequestsEmpty = $operationsTransport->popReceivedCalls();
        $this->assertSame(0, count($operationsRequestsEmpty));
        $actualApiFuncCall = $apiRequests[0]->getFuncCall();
        $actualApiRequestObject = $apiRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/GetBigBook', $actualApiFuncCall);
        $actualValue = $actualApiRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $expectedOperationsRequestObject = new GetOperationRequest();
        $expectedOperationsRequestObject->setName('operations/getBigBookTest');
        $response->pollUntilComplete([
            'initialPollDelayMillis' => 1,
        ]);
        $this->assertTrue($response->isDone());
        $this->assertEquals($expectedResponse, $response->getResult());
        $apiRequestsEmpty = $transport->popReceivedCalls();
        $this->assertSame(0, count($apiRequestsEmpty));
        $operationsRequests = $operationsTransport->popReceivedCalls();
        $this->assertSame(1, count($operationsRequests));
        $actualOperationsFuncCall = $operationsRequests[0]->getFuncCall();
        $actualOperationsRequestObject = $operationsRequests[0]->getRequestObject();
        $this->assertSame('/google.longrunning.Operations/GetOperation', $actualOperationsFuncCall);
        $this->assertEquals($expectedOperationsRequestObject, $actualOperationsRequestObject);
        $this->assertTrue($transport->isExhausted());
        $this->assertTrue($operationsTransport->isExhausted());
    }

    /**
     * @test
     */
    public function getBigBookExceptionTest()
    {
        $operationsTransport = $this->createTransport();
        $operationsClient = new OperationsClient([
            'serviceAddress' => '',
            'transport' => $operationsTransport,
            'credentials' => $this->createCredentials(),
        ]);
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
            'operationsClient' => $operationsClient,
        ]);
        $this->assertTrue($transport->isExhausted());
        $this->assertTrue($operationsTransport->isExhausted());
        // Mock response
        $incompleteOperation = new Operation();
        $incompleteOperation->setName('operations/getBigBookTest');
        $incompleteOperation->setDone(false);
        $transport->addResponse($incompleteOperation);
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $operationsTransport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $response = $client->getBigBook($formattedName);
        $this->assertFalse($response->isDone());
        $this->assertNull($response->getResult());
        $expectedOperationsRequestObject = new GetOperationRequest();
        $expectedOperationsRequestObject->setName('operations/getBigBookTest');
        try {
            $response->pollUntilComplete([
                'initialPollDelayMillis' => 1,
            ]);
            // If the pollUntilComplete() method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stubs are exhausted
        $transport->popReceivedCalls();
        $operationsTransport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
        $this->assertTrue($operationsTransport->isExhausted());
    }

    /**
     * @test
     */
    public function getBigNothingTest()
    {
        $operationsTransport = $this->createTransport();
        $operationsClient = new OperationsClient([
            'serviceAddress' => '',
            'transport' => $operationsTransport,
            'credentials' => $this->createCredentials(),
        ]);
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
            'operationsClient' => $operationsClient,
        ]);
        $this->assertTrue($transport->isExhausted());
        $this->assertTrue($operationsTransport->isExhausted());
        // Mock response
        $incompleteOperation = new Operation();
        $incompleteOperation->setName('operations/getBigNothingTest');
        $incompleteOperation->setDone(false);
        $transport->addResponse($incompleteOperation);
        $expectedResponse = new GPBEmpty();
        $anyResponse = new Any();
        $anyResponse->setValue($expectedResponse->serializeToString());
        $completeOperation = new Operation();
        $completeOperation->setName('operations/getBigNothingTest');
        $completeOperation->setDone(true);
        $completeOperation->setResponse($anyResponse);
        $operationsTransport->addResponse($completeOperation);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $response = $client->getBigNothing($formattedName);
        $this->assertFalse($response->isDone());
        $this->assertNull($response->getResult());
        $apiRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($apiRequests));
        $operationsRequestsEmpty = $operationsTransport->popReceivedCalls();
        $this->assertSame(0, count($operationsRequestsEmpty));
        $actualApiFuncCall = $apiRequests[0]->getFuncCall();
        $actualApiRequestObject = $apiRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/GetBigNothing', $actualApiFuncCall);
        $actualValue = $actualApiRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $expectedOperationsRequestObject = new GetOperationRequest();
        $expectedOperationsRequestObject->setName('operations/getBigNothingTest');
        $response->pollUntilComplete([
            'initialPollDelayMillis' => 1,
        ]);
        $this->assertTrue($response->isDone());
        $this->assertEquals($expectedResponse, $response->getResult());
        $apiRequestsEmpty = $transport->popReceivedCalls();
        $this->assertSame(0, count($apiRequestsEmpty));
        $operationsRequests = $operationsTransport->popReceivedCalls();
        $this->assertSame(1, count($operationsRequests));
        $actualOperationsFuncCall = $operationsRequests[0]->getFuncCall();
        $actualOperationsRequestObject = $operationsRequests[0]->getRequestObject();
        $this->assertSame('/google.longrunning.Operations/GetOperation', $actualOperationsFuncCall);
        $this->assertEquals($expectedOperationsRequestObject, $actualOperationsRequestObject);
        $this->assertTrue($transport->isExhausted());
        $this->assertTrue($operationsTransport->isExhausted());
    }

    /**
     * @test
     */
    public function getBigNothingExceptionTest()
    {
        $operationsTransport = $this->createTransport();
        $operationsClient = new OperationsClient([
            'serviceAddress' => '',
            'transport' => $operationsTransport,
            'credentials' => $this->createCredentials(),
        ]);
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
            'operationsClient' => $operationsClient,
        ]);
        $this->assertTrue($transport->isExhausted());
        $this->assertTrue($operationsTransport->isExhausted());
        // Mock response
        $incompleteOperation = new Operation();
        $incompleteOperation->setName('operations/getBigNothingTest');
        $incompleteOperation->setDone(false);
        $transport->addResponse($incompleteOperation);
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $operationsTransport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $response = $client->getBigNothing($formattedName);
        $this->assertFalse($response->isDone());
        $this->assertNull($response->getResult());
        $expectedOperationsRequestObject = new GetOperationRequest();
        $expectedOperationsRequestObject->setName('operations/getBigNothingTest');
        try {
            $response->pollUntilComplete([
                'initialPollDelayMillis' => 1,
            ]);
            // If the pollUntilComplete() method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stubs are exhausted
        $transport->popReceivedCalls();
        $operationsTransport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
        $this->assertTrue($operationsTransport->isExhausted());
    }

    /**
     * @test
     */
    public function getBookTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name2 = 'name2-1052831874';
        $author = 'author-1406328437';
        $title = 'title110371416';
        $read = true;
        $reader = 'reader-934979389';
        $expectedResponse = new Book();
        $expectedResponse->setName($name2);
        $expectedResponse->setAuthor($author);
        $expectedResponse->setTitle($title);
        $expectedResponse->setRead($read);
        $expectedResponse->setReader($reader);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $response = $client->getBook($formattedName);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/GetBook', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function getBookExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        try {
            $client->getBook($formattedName);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function getBookFromAbsolutelyAnywhereTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name2 = 'name2-1052831874';
        $author = 'author-1406328437';
        $title = 'title110371416';
        $read = true;
        $expectedResponse = new BookFromAnywhere();
        $expectedResponse->setName($name2);
        $expectedResponse->setAuthor($author);
        $expectedResponse->setTitle($title);
        $expectedResponse->setRead($read);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $response = $client->getBookFromAbsolutelyAnywhere($formattedName);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/GetBookFromAbsolutelyAnywhere', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function getBookFromAbsolutelyAnywhereExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        try {
            $client->getBookFromAbsolutelyAnywhere($formattedName);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function getBookFromAnywhereTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name2 = 'name2-1052831874';
        $author = 'author-1406328437';
        $title = 'title110371416';
        $read = true;
        $expectedResponse = new BookFromAnywhere();
        $expectedResponse->setName($name2);
        $expectedResponse->setAuthor($author);
        $expectedResponse->setTitle($title);
        $expectedResponse->setRead($read);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $formattedAltBookName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $formattedPlace = $client->locationName('[PROJECT]', '[LOCATION]');
        $formattedFolder = $client->folderName('[FOLDER]');
        $response = $client->getBookFromAnywhere($formattedName, $formattedAltBookName, $formattedPlace, $formattedFolder);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/GetBookFromAnywhere', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $actualValue = $actualRequestObject->getAltBookName();
        $this->assertProtobufEquals($formattedAltBookName, $actualValue);
        $actualValue = $actualRequestObject->getPlace();
        $this->assertProtobufEquals($formattedPlace, $actualValue);
        $actualValue = $actualRequestObject->getFolder();
        $this->assertProtobufEquals($formattedFolder, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function getBookFromAnywhereExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $formattedAltBookName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $formattedPlace = $client->locationName('[PROJECT]', '[LOCATION]');
        $formattedFolder = $client->folderName('[FOLDER]');
        try {
            $client->getBookFromAnywhere($formattedName, $formattedAltBookName, $formattedPlace, $formattedFolder);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function getBookFromArchiveTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name2 = 'name2-1052831874';
        $author = 'author-1406328437';
        $title = 'title110371416';
        $read = true;
        $expectedResponse = new BookFromArchive();
        $expectedResponse->setName($name2);
        $expectedResponse->setAuthor($author);
        $expectedResponse->setTitle($title);
        $expectedResponse->setRead($read);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->archivedBookName('[ARCHIVE]', '[BOOK]');
        $formattedParent = $client->projectName('[PROJECT]');
        $response = $client->getBookFromArchive($formattedName, $formattedParent);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/GetBookFromArchive', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $actualValue = $actualRequestObject->getParent();
        $this->assertProtobufEquals($formattedParent, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function getBookFromArchiveExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->archivedBookName('[ARCHIVE]', '[BOOK]');
        $formattedParent = $client->projectName('[PROJECT]');
        try {
            $client->getBookFromArchive($formattedName, $formattedParent);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function getShelfTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name2 = 'name2-1052831874';
        $theme = 'theme110327241';
        $internalTheme = 'internalTheme792518087';
        $expectedResponse = new Shelf();
        $expectedResponse->setName($name2);
        $expectedResponse->setTheme($theme);
        $expectedResponse->setInternalTheme($internalTheme);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->shelfName('[SHELF]');
        $options = 'options-1249474914';
        $response = $client->getShelf($formattedName, $options);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/GetShelf', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $actualValue = $actualRequestObject->getOptions();
        $this->assertProtobufEquals($options, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function getShelfExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->shelfName('[SHELF]');
        $options = 'options-1249474914';
        try {
            $client->getShelf($formattedName, $options);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function listAggregatedShelvesTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $nextPageToken = 'nextPageToken-1530815211';
        $expectedResponse = new ListAggregatedShelvesResponse();
        $expectedResponse->setNextPageToken($nextPageToken);
        $transport->addResponse($expectedResponse);
        $response = $client->listAggregatedShelves();
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/ListAggregatedShelves', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function listAggregatedShelvesExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        try {
            $client->listAggregatedShelves();
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function listBooksTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $nextPageToken = '';
        $booksElement = new Book();
        $books = [
            $booksElement,
        ];
        $expectedResponse = new ListBooksResponse();
        $expectedResponse->setNextPageToken($nextPageToken);
        $expectedResponse->setBooks($books);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->shelfName('[SHELF]');
        $response = $client->listBooks($formattedName);
        $this->assertEquals($expectedResponse, $response->getPage()->getResponseObject());
        $resources = iterator_to_array($response->iterateAllElements());
        $this->assertSame(1, count($resources));
        $this->assertEquals($expectedResponse->getBooks()[0], $resources[0]);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/ListBooks', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function listBooksExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->shelfName('[SHELF]');
        try {
            $client->listBooks($formattedName);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function listShelvesTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $nextPageToken = 'nextPageToken-1530815211';
        $expectedResponse = new ListShelvesResponse();
        $expectedResponse->setNextPageToken($nextPageToken);
        $transport->addResponse($expectedResponse);
        $response = $client->listShelves();
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/ListShelves', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function listShelvesExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        try {
            $client->listShelves();
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function listStringsTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $nextPageToken = '';
        $stringsElement = 'stringsElement474465855';
        $strings = [
            $stringsElement,
        ];
        $expectedResponse = new ListStringsResponse();
        $expectedResponse->setNextPageToken($nextPageToken);
        $expectedResponse->setStrings($strings);
        $transport->addResponse($expectedResponse);
        $response = $client->listStrings();
        $this->assertEquals($expectedResponse, $response->getPage()->getResponseObject());
        $resources = iterator_to_array($response->iterateAllElements());
        $this->assertSame(1, count($resources));
        $this->assertEquals($expectedResponse->getStrings()[0], $resources[0]);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/ListStrings', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function listStringsExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        try {
            $client->listStrings();
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function longRunningArchiveBooksTest()
    {
        $operationsTransport = $this->createTransport();
        $operationsClient = new OperationsClient([
            'serviceAddress' => '',
            'transport' => $operationsTransport,
            'credentials' => $this->createCredentials(),
        ]);
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
            'operationsClient' => $operationsClient,
        ]);
        $this->assertTrue($transport->isExhausted());
        $this->assertTrue($operationsTransport->isExhausted());
        // Mock response
        $incompleteOperation = new Operation();
        $incompleteOperation->setName('operations/longRunningArchiveBooksTest');
        $incompleteOperation->setDone(false);
        $transport->addResponse($incompleteOperation);
        $success = false;
        $expectedResponse = new ArchiveBooksResponse();
        $expectedResponse->setSuccess($success);
        $anyResponse = new Any();
        $anyResponse->setValue($expectedResponse->serializeToString());
        $completeOperation = new Operation();
        $completeOperation->setName('operations/longRunningArchiveBooksTest');
        $completeOperation->setDone(true);
        $completeOperation->setResponse($anyResponse);
        $operationsTransport->addResponse($completeOperation);
        $response = $client->longRunningArchiveBooks();
        $this->assertFalse($response->isDone());
        $this->assertNull($response->getResult());
        $apiRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($apiRequests));
        $operationsRequestsEmpty = $operationsTransport->popReceivedCalls();
        $this->assertSame(0, count($operationsRequestsEmpty));
        $actualApiFuncCall = $apiRequests[0]->getFuncCall();
        $actualApiRequestObject = $apiRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/LongRunningArchiveBooks', $actualApiFuncCall);
        $expectedOperationsRequestObject = new GetOperationRequest();
        $expectedOperationsRequestObject->setName('operations/longRunningArchiveBooksTest');
        $response->pollUntilComplete([
            'initialPollDelayMillis' => 1,
        ]);
        $this->assertTrue($response->isDone());
        $this->assertEquals($expectedResponse, $response->getResult());
        $apiRequestsEmpty = $transport->popReceivedCalls();
        $this->assertSame(0, count($apiRequestsEmpty));
        $operationsRequests = $operationsTransport->popReceivedCalls();
        $this->assertSame(1, count($operationsRequests));
        $actualOperationsFuncCall = $operationsRequests[0]->getFuncCall();
        $actualOperationsRequestObject = $operationsRequests[0]->getRequestObject();
        $this->assertSame('/google.longrunning.Operations/GetOperation', $actualOperationsFuncCall);
        $this->assertEquals($expectedOperationsRequestObject, $actualOperationsRequestObject);
        $this->assertTrue($transport->isExhausted());
        $this->assertTrue($operationsTransport->isExhausted());
    }

    /**
     * @test
     */
    public function longRunningArchiveBooksExceptionTest()
    {
        $operationsTransport = $this->createTransport();
        $operationsClient = new OperationsClient([
            'serviceAddress' => '',
            'transport' => $operationsTransport,
            'credentials' => $this->createCredentials(),
        ]);
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
            'operationsClient' => $operationsClient,
        ]);
        $this->assertTrue($transport->isExhausted());
        $this->assertTrue($operationsTransport->isExhausted());
        // Mock response
        $incompleteOperation = new Operation();
        $incompleteOperation->setName('operations/longRunningArchiveBooksTest');
        $incompleteOperation->setDone(false);
        $transport->addResponse($incompleteOperation);
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $operationsTransport->addResponse(null, $status);
        $response = $client->longRunningArchiveBooks();
        $this->assertFalse($response->isDone());
        $this->assertNull($response->getResult());
        $expectedOperationsRequestObject = new GetOperationRequest();
        $expectedOperationsRequestObject->setName('operations/longRunningArchiveBooksTest');
        try {
            $response->pollUntilComplete([
                'initialPollDelayMillis' => 1,
            ]);
            // If the pollUntilComplete() method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stubs are exhausted
        $transport->popReceivedCalls();
        $operationsTransport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
        $this->assertTrue($operationsTransport->isExhausted());
    }

    /**
     * @test
     */
    public function mergeShelvesTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name2 = 'name2-1052831874';
        $theme = 'theme110327241';
        $internalTheme = 'internalTheme792518087';
        $expectedResponse = new Shelf();
        $expectedResponse->setName($name2);
        $expectedResponse->setTheme($theme);
        $expectedResponse->setInternalTheme($internalTheme);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->shelfName('[SHELF]');
        $formattedOtherShelfName = $client->shelfName('[SHELF]');
        $response = $client->mergeShelves($formattedName, $formattedOtherShelfName);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/MergeShelves', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $actualValue = $actualRequestObject->getOtherShelfName();
        $this->assertProtobufEquals($formattedOtherShelfName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function mergeShelvesExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->shelfName('[SHELF]');
        $formattedOtherShelfName = $client->shelfName('[SHELF]');
        try {
            $client->mergeShelves($formattedName, $formattedOtherShelfName);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function moveBookTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name2 = 'name2-1052831874';
        $author = 'author-1406328437';
        $title = 'title110371416';
        $read = true;
        $reader = 'reader-934979389';
        $expectedResponse = new Book();
        $expectedResponse->setName($name2);
        $expectedResponse->setAuthor($author);
        $expectedResponse->setTitle($title);
        $expectedResponse->setRead($read);
        $expectedResponse->setReader($reader);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $formattedOtherShelfName = $client->shelfName('[SHELF]');
        $response = $client->moveBook($formattedName, $formattedOtherShelfName);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/MoveBook', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $actualValue = $actualRequestObject->getOtherShelfName();
        $this->assertProtobufEquals($formattedOtherShelfName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function moveBookExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $formattedOtherShelfName = $client->shelfName('[SHELF]');
        try {
            $client->moveBook($formattedName, $formattedOtherShelfName);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function moveBooksTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $success = false;
        $expectedResponse = new MoveBooksResponse();
        $expectedResponse->setSuccess($success);
        $transport->addResponse($expectedResponse);
        $response = $client->moveBooks();
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/MoveBooks', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function moveBooksExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        try {
            $client->moveBooks();
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function privateListShelvesTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name = 'name3373707';
        $author = 'author-1406328437';
        $title = 'title110371416';
        $read = true;
        $reader = 'reader-934979389';
        $expectedResponse = new Book();
        $expectedResponse->setName($name);
        $expectedResponse->setAuthor($author);
        $expectedResponse->setTitle($title);
        $expectedResponse->setRead($read);
        $expectedResponse->setReader($reader);
        $transport->addResponse($expectedResponse);
        $response = $client->privateListShelves();
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/PrivateListShelves', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function privateListShelvesExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        try {
            $client->privateListShelves();
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function publishSeriesTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new PublishSeriesResponse();
        $transport->addResponse($expectedResponse);
        // Mock request
        $shelf = new Shelf();
        $books = [];
        $seriesUuid = new SeriesUuid();
        $response = $client->publishSeries($shelf, $books, $seriesUuid);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/PublishSeries', $actualFuncCall);
        $actualValue = $actualRequestObject->getShelf();
        $this->assertProtobufEquals($shelf, $actualValue);
        $actualValue = $actualRequestObject->getBooks();
        $this->assertProtobufEquals($books, $actualValue);
        $actualValue = $actualRequestObject->getSeriesUuid();
        $this->assertProtobufEquals($seriesUuid, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function publishSeriesExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $shelf = new Shelf();
        $books = [];
        $seriesUuid = new SeriesUuid();
        try {
            $client->publishSeries($shelf, $books, $seriesUuid);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function saveBookTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new GPBEmpty();
        $transport->addResponse($expectedResponse);
        // Mock request
        $name = 'name3373707';
        $client->saveBook($name);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/SaveBook', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($name, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function saveBookExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $name = 'name3373707';
        try {
            $client->saveBook($name);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function testOptionalRequiredFlatteningParamsTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new TestOptionalRequiredFlatteningParamsResponse();
        $transport->addResponse($expectedResponse);
        // Mock request
        $requiredSingularInt32 = 72313594;
        $requiredSingularInt64 = 72313499;
        $requiredSingularFloat = -7514705.0;
        $requiredSingularDouble = 1.9111005E8;
        $requiredSingularBool = true;
        $requiredSingularEnum = InnerEnum::ZERO;
        $requiredSingularString = 'requiredSingularString-1949894503';
        $requiredSingularBytes = '-29';
        $requiredSingularMessage = new InnerMessage();
        $formattedRequiredSingularResourceName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $formattedRequiredSingularResourceNameOneof = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $requiredSingularResourceNameCommon = 'requiredSingularResourceNameCommon-1126805002';
        $requiredSingularFixed32 = 720656715;
        $requiredSingularFixed64 = 720656810;
        $requiredRepeatedInt32 = [];
        $requiredRepeatedInt64 = [];
        $requiredRepeatedFloat = [];
        $requiredRepeatedDouble = [];
        $requiredRepeatedBool = [];
        $requiredRepeatedEnum = [];
        $requiredRepeatedString = [];
        $requiredRepeatedBytes = [];
        $requiredRepeatedMessage = [];
        $formattedRequiredRepeatedResourceName = [
            $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]'),
        ];
        $formattedRequiredRepeatedResourceNameOneof = [
            $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]'),
        ];
        $requiredRepeatedResourceNameCommon = [];
        $requiredRepeatedFixed32 = [];
        $requiredRepeatedFixed64 = [];
        $requiredMap = [];
        $requiredAnyValue = new Any();
        $requiredStructValue = new Struct();
        $requiredValueValue = new Value();
        $requiredListValueValue = new ListValue();
        $requiredTimeValue = new Timestamp();
        $requiredDurationValue = new Duration();
        $requiredFieldMaskValue = new FieldMask();
        $requiredInt32Value = new Int32Value();
        $requiredUint32Value = new UInt32Value();
        $requiredInt64Value = new Int64Value();
        $requiredUint64Value = new UInt64Value();
        $requiredFloatValue = new FloatValue();
        $requiredDoubleValue = new DoubleValue();
        $requiredStringValue = new StringValue();
        $requiredBoolValue = new BoolValue();
        $requiredBytesValue = new BytesValue();
        $requiredRepeatedAnyValue = [];
        $requiredRepeatedStructValue = [];
        $requiredRepeatedValueValue = [];
        $requiredRepeatedListValueValue = [];
        $requiredRepeatedTimeValue = [];
        $requiredRepeatedDurationValue = [];
        $requiredRepeatedFieldMaskValue = [];
        $requiredRepeatedInt32Value = [];
        $requiredRepeatedUint32Value = [];
        $requiredRepeatedInt64Value = [];
        $requiredRepeatedUint64Value = [];
        $requiredRepeatedFloatValue = [];
        $requiredRepeatedDoubleValue = [];
        $requiredRepeatedStringValue = [];
        $requiredRepeatedBoolValue = [];
        $requiredRepeatedBytesValue = [];
        $response = $client->testOptionalRequiredFlatteningParams($requiredSingularInt32, $requiredSingularInt64, $requiredSingularFloat, $requiredSingularDouble, $requiredSingularBool, $requiredSingularEnum, $requiredSingularString, $requiredSingularBytes, $requiredSingularMessage, $formattedRequiredSingularResourceName, $formattedRequiredSingularResourceNameOneof, $requiredSingularResourceNameCommon, $requiredSingularFixed32, $requiredSingularFixed64, $requiredRepeatedInt32, $requiredRepeatedInt64, $requiredRepeatedFloat, $requiredRepeatedDouble, $requiredRepeatedBool, $requiredRepeatedEnum, $requiredRepeatedString, $requiredRepeatedBytes, $requiredRepeatedMessage, $formattedRequiredRepeatedResourceName, $formattedRequiredRepeatedResourceNameOneof, $requiredRepeatedResourceNameCommon, $requiredRepeatedFixed32, $requiredRepeatedFixed64, $requiredMap, $requiredAnyValue, $requiredStructValue, $requiredValueValue, $requiredListValueValue, $requiredTimeValue, $requiredDurationValue, $requiredFieldMaskValue, $requiredInt32Value, $requiredUint32Value, $requiredInt64Value, $requiredUint64Value, $requiredFloatValue, $requiredDoubleValue, $requiredStringValue, $requiredBoolValue, $requiredBytesValue, $requiredRepeatedAnyValue, $requiredRepeatedStructValue, $requiredRepeatedValueValue, $requiredRepeatedListValueValue, $requiredRepeatedTimeValue, $requiredRepeatedDurationValue, $requiredRepeatedFieldMaskValue, $requiredRepeatedInt32Value, $requiredRepeatedUint32Value, $requiredRepeatedInt64Value, $requiredRepeatedUint64Value, $requiredRepeatedFloatValue, $requiredRepeatedDoubleValue, $requiredRepeatedStringValue, $requiredRepeatedBoolValue, $requiredRepeatedBytesValue);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/TestOptionalRequiredFlatteningParams', $actualFuncCall);
        $actualValue = $actualRequestObject->getRequiredSingularInt32();
        $this->assertProtobufEquals($requiredSingularInt32, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularInt64();
        $this->assertProtobufEquals($requiredSingularInt64, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularFloat();
        $this->assertProtobufEquals($requiredSingularFloat, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularDouble();
        $this->assertProtobufEquals($requiredSingularDouble, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularBool();
        $this->assertProtobufEquals($requiredSingularBool, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularEnum();
        $this->assertProtobufEquals($requiredSingularEnum, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularString();
        $this->assertProtobufEquals($requiredSingularString, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularBytes();
        $this->assertProtobufEquals($requiredSingularBytes, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularMessage();
        $this->assertProtobufEquals($requiredSingularMessage, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularResourceName();
        $this->assertProtobufEquals($formattedRequiredSingularResourceName, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularResourceNameOneof();
        $this->assertProtobufEquals($formattedRequiredSingularResourceNameOneof, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularResourceNameCommon();
        $this->assertProtobufEquals($requiredSingularResourceNameCommon, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularFixed32();
        $this->assertProtobufEquals($requiredSingularFixed32, $actualValue);
        $actualValue = $actualRequestObject->getRequiredSingularFixed64();
        $this->assertProtobufEquals($requiredSingularFixed64, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedInt32();
        $this->assertProtobufEquals($requiredRepeatedInt32, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedInt64();
        $this->assertProtobufEquals($requiredRepeatedInt64, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedFloat();
        $this->assertProtobufEquals($requiredRepeatedFloat, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedDouble();
        $this->assertProtobufEquals($requiredRepeatedDouble, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedBool();
        $this->assertProtobufEquals($requiredRepeatedBool, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedEnum();
        $this->assertProtobufEquals($requiredRepeatedEnum, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedString();
        $this->assertProtobufEquals($requiredRepeatedString, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedBytes();
        $this->assertProtobufEquals($requiredRepeatedBytes, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedMessage();
        $this->assertProtobufEquals($requiredRepeatedMessage, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedResourceName();
        $this->assertProtobufEquals($formattedRequiredRepeatedResourceName, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedResourceNameOneof();
        $this->assertProtobufEquals($formattedRequiredRepeatedResourceNameOneof, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedResourceNameCommon();
        $this->assertProtobufEquals($requiredRepeatedResourceNameCommon, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedFixed32();
        $this->assertProtobufEquals($requiredRepeatedFixed32, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedFixed64();
        $this->assertProtobufEquals($requiredRepeatedFixed64, $actualValue);
        $actualValue = $actualRequestObject->getRequiredMap();
        $this->assertProtobufEquals($requiredMap, $actualValue);
        $actualValue = $actualRequestObject->getRequiredAnyValue();
        $this->assertProtobufEquals($requiredAnyValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredStructValue();
        $this->assertProtobufEquals($requiredStructValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredValueValue();
        $this->assertProtobufEquals($requiredValueValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredListValueValue();
        $this->assertProtobufEquals($requiredListValueValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredTimeValue();
        $this->assertProtobufEquals($requiredTimeValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredDurationValue();
        $this->assertProtobufEquals($requiredDurationValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredFieldMaskValue();
        $this->assertProtobufEquals($requiredFieldMaskValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredInt32Value();
        $this->assertProtobufEquals($requiredInt32Value, $actualValue);
        $actualValue = $actualRequestObject->getRequiredUint32Value();
        $this->assertProtobufEquals($requiredUint32Value, $actualValue);
        $actualValue = $actualRequestObject->getRequiredInt64Value();
        $this->assertProtobufEquals($requiredInt64Value, $actualValue);
        $actualValue = $actualRequestObject->getRequiredUint64Value();
        $this->assertProtobufEquals($requiredUint64Value, $actualValue);
        $actualValue = $actualRequestObject->getRequiredFloatValue();
        $this->assertProtobufEquals($requiredFloatValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredDoubleValue();
        $this->assertProtobufEquals($requiredDoubleValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredStringValue();
        $this->assertProtobufEquals($requiredStringValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredBoolValue();
        $this->assertProtobufEquals($requiredBoolValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredBytesValue();
        $this->assertProtobufEquals($requiredBytesValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedAnyValue();
        $this->assertProtobufEquals($requiredRepeatedAnyValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedStructValue();
        $this->assertProtobufEquals($requiredRepeatedStructValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedValueValue();
        $this->assertProtobufEquals($requiredRepeatedValueValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedListValueValue();
        $this->assertProtobufEquals($requiredRepeatedListValueValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedTimeValue();
        $this->assertProtobufEquals($requiredRepeatedTimeValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedDurationValue();
        $this->assertProtobufEquals($requiredRepeatedDurationValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedFieldMaskValue();
        $this->assertProtobufEquals($requiredRepeatedFieldMaskValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedInt32Value();
        $this->assertProtobufEquals($requiredRepeatedInt32Value, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedUint32Value();
        $this->assertProtobufEquals($requiredRepeatedUint32Value, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedInt64Value();
        $this->assertProtobufEquals($requiredRepeatedInt64Value, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedUint64Value();
        $this->assertProtobufEquals($requiredRepeatedUint64Value, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedFloatValue();
        $this->assertProtobufEquals($requiredRepeatedFloatValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedDoubleValue();
        $this->assertProtobufEquals($requiredRepeatedDoubleValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedStringValue();
        $this->assertProtobufEquals($requiredRepeatedStringValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedBoolValue();
        $this->assertProtobufEquals($requiredRepeatedBoolValue, $actualValue);
        $actualValue = $actualRequestObject->getRequiredRepeatedBytesValue();
        $this->assertProtobufEquals($requiredRepeatedBytesValue, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function testOptionalRequiredFlatteningParamsExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $requiredSingularInt32 = 72313594;
        $requiredSingularInt64 = 72313499;
        $requiredSingularFloat = -7514705.0;
        $requiredSingularDouble = 1.9111005E8;
        $requiredSingularBool = true;
        $requiredSingularEnum = InnerEnum::ZERO;
        $requiredSingularString = 'requiredSingularString-1949894503';
        $requiredSingularBytes = '-29';
        $requiredSingularMessage = new InnerMessage();
        $formattedRequiredSingularResourceName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $formattedRequiredSingularResourceNameOneof = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $requiredSingularResourceNameCommon = 'requiredSingularResourceNameCommon-1126805002';
        $requiredSingularFixed32 = 720656715;
        $requiredSingularFixed64 = 720656810;
        $requiredRepeatedInt32 = [];
        $requiredRepeatedInt64 = [];
        $requiredRepeatedFloat = [];
        $requiredRepeatedDouble = [];
        $requiredRepeatedBool = [];
        $requiredRepeatedEnum = [];
        $requiredRepeatedString = [];
        $requiredRepeatedBytes = [];
        $requiredRepeatedMessage = [];
        $formattedRequiredRepeatedResourceName = [
            $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]'),
        ];
        $formattedRequiredRepeatedResourceNameOneof = [
            $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]'),
        ];
        $requiredRepeatedResourceNameCommon = [];
        $requiredRepeatedFixed32 = [];
        $requiredRepeatedFixed64 = [];
        $requiredMap = [];
        $requiredAnyValue = new Any();
        $requiredStructValue = new Struct();
        $requiredValueValue = new Value();
        $requiredListValueValue = new ListValue();
        $requiredTimeValue = new Timestamp();
        $requiredDurationValue = new Duration();
        $requiredFieldMaskValue = new FieldMask();
        $requiredInt32Value = new Int32Value();
        $requiredUint32Value = new UInt32Value();
        $requiredInt64Value = new Int64Value();
        $requiredUint64Value = new UInt64Value();
        $requiredFloatValue = new FloatValue();
        $requiredDoubleValue = new DoubleValue();
        $requiredStringValue = new StringValue();
        $requiredBoolValue = new BoolValue();
        $requiredBytesValue = new BytesValue();
        $requiredRepeatedAnyValue = [];
        $requiredRepeatedStructValue = [];
        $requiredRepeatedValueValue = [];
        $requiredRepeatedListValueValue = [];
        $requiredRepeatedTimeValue = [];
        $requiredRepeatedDurationValue = [];
        $requiredRepeatedFieldMaskValue = [];
        $requiredRepeatedInt32Value = [];
        $requiredRepeatedUint32Value = [];
        $requiredRepeatedInt64Value = [];
        $requiredRepeatedUint64Value = [];
        $requiredRepeatedFloatValue = [];
        $requiredRepeatedDoubleValue = [];
        $requiredRepeatedStringValue = [];
        $requiredRepeatedBoolValue = [];
        $requiredRepeatedBytesValue = [];
        try {
            $client->testOptionalRequiredFlatteningParams($requiredSingularInt32, $requiredSingularInt64, $requiredSingularFloat, $requiredSingularDouble, $requiredSingularBool, $requiredSingularEnum, $requiredSingularString, $requiredSingularBytes, $requiredSingularMessage, $formattedRequiredSingularResourceName, $formattedRequiredSingularResourceNameOneof, $requiredSingularResourceNameCommon, $requiredSingularFixed32, $requiredSingularFixed64, $requiredRepeatedInt32, $requiredRepeatedInt64, $requiredRepeatedFloat, $requiredRepeatedDouble, $requiredRepeatedBool, $requiredRepeatedEnum, $requiredRepeatedString, $requiredRepeatedBytes, $requiredRepeatedMessage, $formattedRequiredRepeatedResourceName, $formattedRequiredRepeatedResourceNameOneof, $requiredRepeatedResourceNameCommon, $requiredRepeatedFixed32, $requiredRepeatedFixed64, $requiredMap, $requiredAnyValue, $requiredStructValue, $requiredValueValue, $requiredListValueValue, $requiredTimeValue, $requiredDurationValue, $requiredFieldMaskValue, $requiredInt32Value, $requiredUint32Value, $requiredInt64Value, $requiredUint64Value, $requiredFloatValue, $requiredDoubleValue, $requiredStringValue, $requiredBoolValue, $requiredBytesValue, $requiredRepeatedAnyValue, $requiredRepeatedStructValue, $requiredRepeatedValueValue, $requiredRepeatedListValueValue, $requiredRepeatedTimeValue, $requiredRepeatedDurationValue, $requiredRepeatedFieldMaskValue, $requiredRepeatedInt32Value, $requiredRepeatedUint32Value, $requiredRepeatedInt64Value, $requiredRepeatedUint64Value, $requiredRepeatedFloatValue, $requiredRepeatedDoubleValue, $requiredRepeatedStringValue, $requiredRepeatedBoolValue, $requiredRepeatedBytesValue);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function updateBookTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name2 = 'name2-1052831874';
        $author = 'author-1406328437';
        $title = 'title110371416';
        $read = true;
        $reader = 'reader-934979389';
        $expectedResponse = new Book();
        $expectedResponse->setName($name2);
        $expectedResponse->setAuthor($author);
        $expectedResponse->setTitle($title);
        $expectedResponse->setRead($read);
        $expectedResponse->setReader($reader);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $book = new Book();
        $response = $client->updateBook($formattedName, $book);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/UpdateBook', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $actualValue = $actualRequestObject->getBook();
        $this->assertProtobufEquals($book, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function updateBookExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $book = new Book();
        try {
            $client->updateBook($formattedName, $book);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function updateBookIndexTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $expectedResponse = new GPBEmpty();
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $indexName = 'indexName746962392';
        $indexMap = [];
        $client->updateBookIndex($formattedName, $indexName, $indexMap);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.example.library.v1.LibraryService/UpdateBookIndex', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $actualValue = $actualRequestObject->getIndexName();
        $this->assertProtobufEquals($indexName, $actualValue);
        $actualValue = $actualRequestObject->getIndexMap();
        $this->assertProtobufEquals($indexMap, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

    /**
     * @test
     */
    public function updateBookIndexExceptionTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        $status = new stdClass();
        $status->code = Code::DATA_LOSS;
        $status->details = 'internal error';
        $expectedExceptionMessage  = json_encode([
            'message' => 'internal error',
            'code' => Code::DATA_LOSS,
            'status' => 'DATA_LOSS',
            'details' => [],
        ], JSON_PRETTY_PRINT);
        $transport->addResponse(null, $status);
        // Mock request
        $formattedName = $client->bookName('[SHELF]', '[BOOK_ONE]', '[BOOK_TWO]');
        $indexName = 'indexName746962392';
        $indexMap = [];
        try {
            $client->updateBookIndex($formattedName, $indexName, $indexMap);
            // If the $client method call did not throw, fail the test
            $this->fail('Expected an ApiException, but no exception was thrown.');
        } catch (ApiException $ex) {
            $this->assertEquals($status->code, $ex->getCode());
            $this->assertEquals($expectedExceptionMessage, $ex->getMessage());
        }
        // Call popReceivedCalls to ensure the stub is exhausted
        $transport->popReceivedCalls();
        $this->assertTrue($transport->isExhausted());
    }
}
