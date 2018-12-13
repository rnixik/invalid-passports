<?php

namespace App\Service;

class InvalidPassportsServiceIncludeSeries implements InvalidPassportsServiceInterface
{
    protected const STORAGE_PATH = '/mnt/tmpfs/';

    protected $buffer = [];

    /**
     * @inheritdoc
     */
    public function isValid(string $series, string $number): bool
    {
        if (!is_readable(self::STORAGE_PATH)) {
            throw new \RuntimeException("Storage path '" . self::STORAGE_PATH . "' is not readable");
        }

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
        if (!isset($this->buffer[$series])) {
            $this->buffer[$series] = [];
        }
        $this->buffer[$series][] = $number;
    }

    /**
     * @inheritdoc
     */
    public function flushBufferToStore(): void
    {
        $this->clearStorage();

        foreach ($this->buffer as $series => $numbers) {
            $subStr = '';
            foreach ($numbers as $number) {
                $subStr .= "'$number'=>true,";
            }
            $str = '<?php return [';
            $str .= $subStr;
            $str .= '];';
            file_put_contents($this->getPathForSeries($series), $str);
        }

        $this->buffer = [];
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
