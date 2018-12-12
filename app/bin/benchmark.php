<?php

require __DIR__ . '/../vendor/autoload.php';

$invalidSeries = '7504';
$invalidNumber = '328185';
$service = \App\Application::getDefaultImplementation();

for ($i = 0; $i < 10; $i += 1) {
    $series = 1234;
    $number = 123456;
    if (rand(1, 10) > 7) {
        $series = $invalidSeries;
        $number = $invalidNumber;
    }
    $url = "http://nginx/index.php?series={$series}&number={$number}&service={$service}";
    $startTime = microtime(true);
    $response = file_get_contents($url);
    $timeInSeconds = (microtime(true) - $startTime);
    $time = number_format(round($timeInSeconds * 1000 * 10) / 10, 1);
    echo "{$time}ms $series $number: $response" . PHP_EOL;
}

echo 'Done' . PHP_EOL;
