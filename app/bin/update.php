<?php

require __DIR__ . '/../vendor/autoload.php';

$fileName = '/tmp/list_of_expired_passports.csv';
if (!file_exists($fileName)) {
    exec(__DIR__ . '/download-file.sh');
}

$application = new \App\Application();
$storageService = $application->getInvalidPassportsService();
$parser = new \App\Service\SourceParser();

$added = $parser->parseAndStore($fileName, $storageService);
echo "Added: $added." . PHP_EOL;
