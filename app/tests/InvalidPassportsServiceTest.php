<?php

namespace App\Tests;

use App\Service\BadRequestException;
use App\Service\InvalidPassportsService;
use PHPUnit\Framework\TestCase;
use Predis\Client;

class InvalidPassportsServiceTest extends TestCase
{
    /** @var InvalidPassportsService */
    protected $service;

    /** @var Client */
    protected $redisStub;

    protected function setUp()
    {
        $this->redisStub = $this->createMock(Client::class);
        $this->service = new InvalidPassportsService($this->redisStub);
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

    public function testIsValidExceptionSeries()
    {
        $this->expectException(BadRequestException::class);
        $this->service->isValid(111, 223344);
    }

    public function testIsValidExceptionNumber()
    {
        $this->expectException(BadRequestException::class);
        $this->service->isValid(1111, 23344);
    }
}
