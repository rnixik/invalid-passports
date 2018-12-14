<?php

namespace App\Service;

class InvalidPassportsServiceIncludeSeries implements InvalidPassportsServiceInterface
{
    protected const STORAGE_PATH = '/mnt/tmpfs/';
    protected const STORAGE_BUFFER_PATH = '/mnt/tmpfs_buffer/';

    protected $buffer = [];

    protected $openFiles = [];

    /**
     * @inheritdoc
     */
    public function isValid(string $series, string $number): bool
    {
        if (!is_readable(self::STORAGE_PATH)) {
            throw new \RuntimeException("Storage path '" . self::STORAGE_PATH . "' is not readable");
        }

        $series = intval($series);
        $number = intval($number);

        $path = $this->getPathForSeries($series);
        if (file_exists($path)) {
            $data = include($path);
            return !isset($data[$number]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function addRecordToStoreBuffer(string $series, string $number): void
    {
        $series = intval($series);
        $number = intval($number);
        if (isset($this->openFiles[$series])) {
            $file = $this->openFiles[$series];
        } else {
            $file = new \SplFileObject(self::STORAGE_BUFFER_PATH . $series . '.php', "w");
            $this->openFiles[$series] = $file;
            $file->fwrite("<?php return [");
        }

        $file->fwrite("$number=>true,");
    }

    /**
     * @inheritdoc
     */
    public function flushBufferToStore(): void
    {
        $this->clearStorage();
        /** @var \SplFileObject $file */
        foreach ($this->openFiles as $series => $file) {
            $file->fwrite("];");
            copy($file->getRealPath(), $this->getPathForSeries($series));
            unlink($file->getRealPath());
        }
        $this->openFiles = [];
    }

    /**
     * @inheritdoc
     */
    public function prepareCache(): void
    {
        $this->isValid(1111, 223344);
    }

    protected function clearStorage()
    {
        for ($i = 0; $i <= 9999; $i += 1) {
            $path = $this->getPathForSeries($i);
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    protected function getPathForSeries(string $series): string
    {
        return self::STORAGE_PATH . $series . '.php';
    }
}
