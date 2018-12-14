<?php

namespace App;

use App\Controller\DefaultController;
use App\Service\InvalidPassportsServiceInclude;
use App\Service\InvalidPassportsServiceIncludeParts;
use App\Service\InvalidPassportsServiceIncludeSeries;
use App\Service\InvalidPassportsServiceInterface;
use App\Service\InvalidPassportsServiceRedis;
use App\Service\InvalidPassportsServiceShmop;

class Application
{
    public const IMPLEMENTATION_REDIS = 'redis';
    public const IMPLEMENTATION_INCLUDE = 'include';
    public const IMPLEMENTATION_INCLUDE_PARTS = 'include_parts';
    public const IMPLEMENTATION_INCLUDE_SERIES = 'include_series';
    public const IMPLEMENTATION_SHMOP = 'shmop';

    public function run()
    {
        $this->setErrorHandler();

        $service = $this->getInvalidPassportsService();
        $controller = new DefaultController($service);
        if ($_SERVER['REQUEST_URI'] === '/reset-cache') {
            $controller->resetCache();
            exit;
        }
        if ($_SERVER['REQUEST_URI'] === '/prepare-cache') {
            $controller->prepareCache();
            exit;
        }
        $controller->validatePassport();
    }

    public static function getDefaultImplementation()
    {
        return self::IMPLEMENTATION_INCLUDE_PARTS;
    }

    /**
     * @return InvalidPassportsServiceInterface
     */
    public function getInvalidPassportsService(): InvalidPassportsServiceInterface
    {
        $serviceName = self::getDefaultImplementation();

        switch ($serviceName) {
            case self::IMPLEMENTATION_REDIS:
                $redis = new \Predis\Client(['host' => 'redis']);
                $service = new InvalidPassportsServiceRedis($redis);
                break;
            case self::IMPLEMENTATION_SHMOP:
                $service = new InvalidPassportsServiceShmop();
                break;
            case self::IMPLEMENTATION_INCLUDE:
                $service = new InvalidPassportsServiceInclude();
                break;
            case self::IMPLEMENTATION_INCLUDE_SERIES:
                $service = new InvalidPassportsServiceIncludeSeries();
                break;
            case self::IMPLEMENTATION_INCLUDE_PARTS:
                $service = new InvalidPassportsServiceIncludeParts();
                break;
            default:
                throw new \RuntimeException("Implementation is not defined");
        }

        return $service;
    }

    protected function setErrorHandler()
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            throw new \ErrorException($errstr);
        });
    }
}
