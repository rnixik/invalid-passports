<?php

namespace App\Service;

use Tarantool\Client\Client;
use Tarantool\Client\Exception\Exception;
use Tarantool\Client\Schema\Space;

class InvalidPassportsServiceTarantool implements InvalidPassportsServiceInterface
{
    /** @var Space */
    protected $space;

    public function __construct(Client $client)
    {
        $this->space = $client->getSpace('invalid_passports');
    }

    /**
     * @inheritdoc
     */
    public function isValid(string $series, string $number): bool
    {
        $key = $this->getKey($series, $number);
        $response = $this->space->select([$key]);
        $result = $response->getData();
        return !(!empty($result) && !empty($result[0]));
    }

    /**
     * @inheritdoc
     */
    public function addRecordToStoreBuffer(string $series, string $number): void
    {
        $key = $this->getKey($series, $number);
        try {
            $this->space->insert([$key]);
        } catch (Exception $exception) {
            // Duplicate
        }
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
        // Nothing to do here
    }

    /**
     * @param string $series
     * @param string $number
     * @return int
     */
    protected function getKey(string $series, string $number): int
    {
        return intval($series . $number);
    }
}
