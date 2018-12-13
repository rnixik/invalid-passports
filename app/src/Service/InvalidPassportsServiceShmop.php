<?php

namespace App\Service;

class InvalidPassportsServiceShmop implements InvalidPassportsServiceInterface
{
    protected const DEFAULT_ID_KEY = 0xff5;

    // If size changes, DEFAULT_ID_KEY should be changed also
    protected const MAX_DATA_SIZE = 2147483648; //2GB

    protected $buffer = [];

    protected $key;

    public function __construct($key = self::DEFAULT_ID_KEY)
    {
        $this->key = $key;
    }

    /**
     * @inheritdoc
     */
    public function isValid(string $series, string $number): bool
    {
        $key = $this->getKey($series, $number);

        $resource = @shmop_open($this->key, 'a', 0, 0);
        if (!empty($resource)) {
            $dataStr = shmop_read($resource, 0, 0);
            if ($dataStr) {
                $data = unserialize($dataStr);
                if (!is_array($data)) {
                    throw new \RuntimeException("Shmop data is array");
                }
                $dataStr = '';
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
        $this->buffer = [];
        $dataSize = strlen($dataStr);
        if ($dataSize >= self::MAX_DATA_SIZE) {
            throw new \RuntimeException("DataSize exceeded limit");
        }
        $resourceToDelete = @shmop_open($this->key, 'c', 0, 0);
        if ($resourceToDelete) {
            shmop_delete($resourceToDelete);
            shmop_close($resourceToDelete);
        }
        $resource = shmop_open($this->key, 'c', 0600, $dataSize);
        shmop_write($resource, $dataStr, 0);
    }

    protected function getKey(string $series, string $number): string
    {
        return $series . $number;
    }
}
