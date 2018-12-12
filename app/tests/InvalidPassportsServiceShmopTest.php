<?php

namespace App\Tests;

use App\Service\InvalidPassportsServiceShmop;
use PHPUnit\Framework\TestCase;

class InvalidPassportsServiceShmopTest extends TestCase
{
    /** @var InvalidPassportsServiceShmop */
    protected $service;

    protected function setUp()
    {
        $this->service = new InvalidPassportsServiceShmop();
    }

    protected function tearDown()
    {
        $this->service->flushBufferToStore();
    }

    public function testIsValidValid()
    {
        $this->service->addRecordToStoreBuffer(2222, 444555);
        $this->service->flushBufferToStore();

        $this->assertTrue($this->service->isValid(1111, 223344));
    }

    public function testIsValidInvalid()
    {
        $this->service->addRecordToStoreBuffer(1111, 223344);
        $this->service->flushBufferToStore();

        $this->assertFalse($this->service->isValid(1111, 223344));
    }
}
