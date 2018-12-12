<?php

namespace App\Service;

use Predis\Client;

class InvalidPassportsService
{
    /** @var Client */
    protected $redis;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Validates series and number of passport by expiration
     *
     * @param string $series length should be equal to 4
     * @param string $number length should be equal to 6
     * @return bool
     */
    public function isValid(string $series, string $number): bool
    {
        $key = $this->getKey($series, $number);
        return !$this->redis->exists($key);
    }

    /**
     * @param string $series
     * @param string $number
     * @return string
     * @throws BadRequestException
     */
    protected function getKey(string $series, string $number): string
    {
        if (strlen($series) !== 4) {
            throw new BadRequestException("Length of series must be 4");
        }
        if (strlen($number) !== 6) {
            throw new BadRequestException("Length of number must be 6");
        }

        return $series . $number;
    }
}