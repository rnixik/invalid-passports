<?php

namespace App\Service;

class InvalidPassportsServiceShmop implements InvalidPassportsServiceInterface
{
    protected const ID_KEY = 0xff1;

    protected $buffer = [];

    /**
     * @inheritdoc
     */
    public function isValid(string $series, string $number): bool
    {
        $key = $this->getKey($series, $number);

        $resource = @shmop_open(self::ID_KEY, 'a', 0, 0);
        if (!empty($resource)) {
            $dataStr = shmop_read($resource, 0, 0);
            if ($dataStr) {
                $data = unserialize($dataStr);
                return !isset($data[$key]);
            }
        }

        throw new \RuntimeException("Shmop is not prepared");
    }

    /**
     * @inheritdoc
     */
    public function addRecordToStoreBuffer(string $series, string $number): void
    {
        $key = $this->getKey($series, $number);
        $this->buffer[$key] = true;
    }

    /**
     * @inheritdoc
     */
    public function flushBufferToStore(): void
    {
        $dataStr = serialize($this->buffer);
        $dataSize = strlen($dataStr);
        $resource = shmop_open(self::ID_KEY, 'c', 0600, $dataSize);
        shmop_write($resource, $dataStr, 0);
        $this->buffer = [];
    }

    protected function getKey(string $series, string $number): string
    {
        return $series . $number;
    }
}
