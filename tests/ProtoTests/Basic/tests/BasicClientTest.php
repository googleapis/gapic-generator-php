<?php

declare(strict_types=1);

namespace testing\basic;

use Google\ApiCore\Testing\GeneratedTest;
use Google\ApiCore\Testing\MockTransport;
use Google\ApiCore\Transport\TransportInterface;

class BasicClientTest extends GeneratedTest
{
    /** @return TransportInterface */
    private function createTransport($deserialize = null)
    {
        return new MockTransport($deserialize);
    }
}
