<?php

namespace App\Service;

class InvalidPassportsServiceInclude implements InvalidPassportsServiceInterface
{
    protected const STORAGE_PATH = '/mnt/tmpfs/data.php';

    protected $buffer = '';

    public function __construct()
    {
        $this->buffer = '<?php return [' . PHP_EOL;
    }

    /**
     * @inheritdoc
     */
    public function isValid(string $series, string $number): bool
    {
        if (!is_readable(self::STORAGE_PATH)) {
            throw new \RuntimeException("Storage path '" . self::STORAGE_PATH . "' is not readable");
        }
        $data = include(self::STORAGE_PATH);
        $key = $this->getKey($series, $number);
        return !isset($data[$key]);
    }

    /**
     * @inheritdoc
     */
    public function addRecordToStoreBuffer(string $series, string $number): void
    {
        $key = $this->getKey($series, $number);
        $this->buffer .= "'$key' => true," . PHP_EOL;
    }

    /**
     * @inheritdoc
     */
    public function flushBufferToStore(): void
    {
        $this->buffer .= '];' . PHP_EOL;
        file_put_contents(self::STORAGE_PATH, $this->buffer);
        $this->buffer = '';
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
