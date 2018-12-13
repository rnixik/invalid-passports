<?php

namespace App\Service;

class InvalidPassportsServiceInclude implements InvalidPassportsServiceInterface
{
    protected const STORAGE_FILE_PATH = '/mnt/tmpfs/data.php';
    protected const STORAGE_BUFFER_FILE_PATH = '/mnt/tmpfs/data_buffer.php';

    /** @var \SplFileObject|null */
    protected $bufferFile;

    /**
     * @inheritdoc
     */
    public function isValid(string $series, string $number): bool
    {
        if (!is_readable(self::STORAGE_FILE_PATH)) {
            throw new \RuntimeException("Storage file '" . self::STORAGE_FILE_PATH . "' is not readable");
        }
        $data = include(self::STORAGE_FILE_PATH);
        $key = $this->getKey($series, $number);
        return !isset($data[$key]);
    }

    /**
     * @inheritdoc
     */
    public function addRecordToStoreBuffer(string $series, string $number): void
    {
        if (!$this->bufferFile) {
            $this->bufferFile = $this->initBufferFile();
        }
        $key = $this->getKey($series, $number);
        $this->bufferFile->fwrite("'$key'=>true,");
    }

    /**
     * @inheritdoc
     */
    public function flushBufferToStore(): void
    {
        if (!$this->bufferFile) {
            $this->bufferFile = $this->initBufferFile();
        }
        $this->bufferFile->fwrite("];");
        copy($this->bufferFile->getRealPath(), self::STORAGE_FILE_PATH);
        unlink($this->bufferFile->getRealPath());
        $this->bufferFile = null;
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

    protected function initBufferFile(): \SplFileObject
    {
        $bufferFile = new \SplFileObject(self::STORAGE_BUFFER_FILE_PATH, 'w');
        $bufferFile->fwrite('<?php return[');
        return $bufferFile;
    }
}
