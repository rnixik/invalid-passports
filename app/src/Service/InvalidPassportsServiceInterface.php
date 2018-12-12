<?php

namespace App\Service;

interface InvalidPassportsServiceInterface
{
    /**
     * Validates series and number of passport by expiration
     *
     * @param string $series length should be equal to 4
     * @param string $number length should be equal to 6
     * @return bool
     * @throws \RuntimeException
     */
    public function isValid(string $series, string $number): bool;

    /**
     * Adds new pair of series and number to buffer for storing.
     *
     * Call self::flushBufferToStore to save all added records.
     *
     * @param string $series
     * @param string $number
     */
    public function addRecordToStoreBuffer(string $series, string $number): void;

    /**
     * Updates store with actual values of invalid pairs from buffer.
     *
     * @see self::addRecordToStoreBuffer
     */
    public function flushBufferToStore(): void;
}
