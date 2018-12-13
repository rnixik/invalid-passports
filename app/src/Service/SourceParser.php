<?php

namespace App\Service;

class SourceParser
{
    /**
     * @param string $sourceFilePath
     * @param InvalidPassportsServiceInterface $storage
     * @return int
     * @throws \RuntimeException
     */
    public function parseAndStore(string $sourceFilePath, InvalidPassportsServiceInterface $storage): int
    {
        $rowsNumber = 0;

        $file = new \SplFileObject($sourceFilePath);
        $file->setFlags(\SplFileObject::READ_CSV);

        foreach ($file as $i => $row) {
            if ($i == 0) {
                // Skip header
                continue;
            }
            list($series, $number) = $row;
            if (strlen($series) !== 4
                || strlen($number) !== 6
                || !is_numeric($series)
                || !is_numeric($number)) {
                continue;
            }
            $storage->addRecordToStoreBuffer($series, $number);
            $rowsNumber++;
        }

        if ($rowsNumber){
            $storage->flushBufferToStore();
        }

        return $rowsNumber;
    }
}
