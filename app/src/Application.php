<?php

namespace App;

use App\Controller\DefaultController;
use App\Service\InvalidPassportsServiceInclude;
use App\Service\InvalidPassportsServiceInterface;
use App\Service\InvalidPassportsServiceRedis;
use App\Service\InvalidPassportsServiceShmop;

class Application
{
    public const IMPLEMENTATION_REDIS = 'redis';
    public const IMPLEMENTATION_INCLUDE = 'include';
    public const IMPLEMENTATION_SHMOP = 'shmop';

    public function run()
    {
        $service = $this->getInvalidPassportsService();
        $controller = new DefaultController($service);
        $controller->validatePassport();
    }

    public static function getDefaultImplementation()
    {
        return self::IMPLEMENTATION_SHMOP;
    }

    /**
     * @return InvalidPassportsServiceInterface
     */
    public function getInvalidPassportsService(): InvalidPassportsServiceInterface
    {
        $serviceNameFromRequest = $_REQUEST['service'] ?? self::getDefaultImplementation();

        switch ($serviceNameFromRequest) {
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
            default:
                throw new \RuntimeException("Implementation is not defined");
        }

        return $service;
    }
}
