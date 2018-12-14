<?php

namespace App\Service;

class InvalidPassportsServiceIncludeParts implements InvalidPassportsServiceInterface
{
    protected const STORAGE_PATH = '/mnt/tmpfs';
    protected const STORAGE_BUFFER_PATH = '/mnt/tmpfs_buffer';

    protected const PARTS_NUMBER = 10;

    /** @var \SplFileObject[] */
    protected $bufferFiles = [];

    /**
     * @inheritdoc
     */
    public function isValid(string $series, string $number): bool
    {
        $key = $this->getKey($series, $number);
        $partNumber = $key % self::PARTS_NUMBER;
        $storageFilePath = self::STORAGE_PATH . '/' . $this->getPartFileName($partNumber);
        if (!is_readable($storageFilePath)) {
            throw new \RuntimeException("Storage file '" . $storageFilePath . "' is not readable");
        }

        $data = include($storageFilePath);
        return !isset($data[$key]);
    }

    /**
     * @inheritdoc
     */
    public function addRecordToStoreBuffer(string $series, string $number): void
    {
        if (!$this->bufferFiles) {
            $this->bufferFiles = $this->initBufferFiles();
        }
        $key = $this->getKey($series, $number);
        $partIndex = $key % self::PARTS_NUMBER;
        $this->bufferFiles[$partIndex]->fwrite("'$key'=>true,");
    }

    /**
     * @inheritdoc
     */
    public function flushBufferToStore(): void
    {
        if (!$this->bufferFiles) {
            $this->bufferFiles = $this->initBufferFiles();
        }
        foreach ($this->bufferFiles as $partNumber => $file) {
            $file->fwrite("];");
            copy($file->getRealPath(), self::STORAGE_PATH . '/' . $this->getPartFileName($partNumber));
            unlink($file->getRealPath());
        }

        $this->bufferFiles = [];
    }

    /**
     * @inheritdoc
     */
    public function prepareCache(): void
    {
        for ($i = 0; $i < self::PARTS_NUMBER; $i += 1) {
            $this->isValid('0000', '00000' . $i);
        }
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

    protected function initBufferFiles(): array
    {
        $files = [];

        for ($i = 0; $i < self::PARTS_NUMBER; $i += 1) {
            $files[$i] = $this->initBufferFile($i);
        }

        return $files;
    }

    protected function initBufferFile(int $partNumber): \SplFileObject
    {
        $bufferFile = new \SplFileObject(self::STORAGE_BUFFER_PATH . '/' . $this->getPartFileName($partNumber), 'w');
        $bufferFile->fwrite('<?php return[');
        return $bufferFile;
    }

    protected function getPartFileName(int $partNumber): string
    {
        return "part_$partNumber.php";
    }
}
