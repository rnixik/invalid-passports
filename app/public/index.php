<?php

require __DIR__ . '/../vendor/autoload.php';

$controller = new \App\Controller\DefaultController();
$controller->validatePassport();
