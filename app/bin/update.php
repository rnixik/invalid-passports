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

echo "Reset cache: " . file_get_contents('http://nginx/reset-cache') . PHP_EOL;
// Need time to free cache
sleep(3);
echo "Prepare cache: " . file_get_contents('http://nginx/prepare-cache') . PHP_EOL;
