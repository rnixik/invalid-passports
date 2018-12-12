<?php

require __DIR__ . '/../vendor/autoload.php';

opcache_reset();
$application = new \App\Application();
$application->run();
