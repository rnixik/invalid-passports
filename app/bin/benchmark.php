<?php

$invalidSeries = '7504';
$invalidNumber = '328185';

for ($i = 0; $i < 10; $i += 1) {
    $series = 1234 - $i;
    $number = 123456 + $i;
    if (rand(1, 10) > 7) {
        $series = $invalidSeries;
        $number = $invalidNumber;
    }
    $url = "http://nginx/?series={$series}&number={$number}";
    $startTime = microtime(true);
    $response = file_get_contents($url);
    $timeInSeconds = (microtime(true) - $startTime);
    $time = number_format(round($timeInSeconds * 1000 * 10) / 10, 1, '.', '');
    echo "{$time}ms $series $number: $response" . PHP_EOL;
}

echo 'Done' . PHP_EOL;
