<?php

for ($i = 0; $i < 10; $i += 1) {
    $startTime = microtime(true);
    $url = "http://nginx/index.php?series=1234&number=123456";
    $response = file_get_contents($url);
    $timeInSeconds = (microtime(true) - $startTime);
    $time = round($timeInSeconds * 1000);
    echo "{$time}ms: $response" . PHP_EOL;
}
echo 'Done' . PHP_EOL;
