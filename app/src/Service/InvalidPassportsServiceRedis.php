<?php

namespace App\Service;

use Predis\Client;

class InvalidPassportsServiceRedis implements InvalidPassportsServiceInterface
{
    /**
     * Depends on how often source is updated
     */
    protected const KEY_TTL = 3600 * 24;

    /** @var Client */
    protected $redis;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @inheritdoc
     */
    public function isValid(string $series, string $number): bool
    {
        $key = $this->getKey($series, $number);
        return !$this->redis->exists($key);
    }

    /**
     * @inheritdoc
     */
    public function addRecordToStoreBuffer(string $series, string $number): void
    {
        $key = $this->getKey($series, $number);
        $this->redis->set($key, true, 'EX', self::KEY_TTL);
    }

    /**
     * @inheritdoc
     */
    public function prepareCache(): void
    {
        $this->isValid(1111, 223344);
    }

    /**
     * @inheritdoc
     */
    public function flushBufferToStore(): void
    {
        // Nothing to do
    }

    /**
     * @param string $series
     * @param string $number
     * @return string
     */
    protected function getKey(string $series, string $number): string
    {
        return $series . $number;
    }
}
