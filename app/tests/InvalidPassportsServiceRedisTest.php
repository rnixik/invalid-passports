<?php

namespace App\Tests;

use App\Service\InvalidPassportsServiceRedis;
use PHPUnit\Framework\TestCase;
use Predis\Client;

class InvalidPassportsServiceRedisTest extends TestCase
{
    /** @var InvalidPassportsServiceRedis */
    protected $service;

    /** @var Client */
    protected $redisStub;

    protected function setUp()
    {
        $this->redisStub = $this->createMock(Client::class);
        $this->service = new InvalidPassportsServiceRedis($this->redisStub);
    }

    public function testIsValidValid()
    {
        $this->redisStub->method('__call')->with(
            $this->equalTo('exists'),
            $this->anything()
        )->willReturn(false);

        $this->assertTrue($this->service->isValid(1111, 223344));
    }

    public function testIsValidInvalid()
    {
        $this->redisStub->method('__call')->with(
            $this->equalTo('exists'),
            $this->anything()
        )->willReturn(true);

        $this->assertFalse($this->service->isValid(1111, 223344));
    }

    public function testAddRecordToStoreBuffer()
    {
        $this->redisStub->expects($this->once())->method('__call')->with(
            $this->equalTo('set'),
            $this->anything()
        );

        $this->service->addRecordToStoreBuffer(1111, 22333444);
    }
}
