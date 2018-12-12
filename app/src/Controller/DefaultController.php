<?php

namespace App\Controller;

use App\Service\BadRequestException;
use App\Service\InvalidPassportsService;

class DefaultController
{
    /** @var InvalidPassportsService */
    protected $invalidPassportsService;

    public function __construct()
    {
        $redis = new \Predis\Client([
            'host' => 'redis',
        ]);
        $this->invalidPassportsService = new InvalidPassportsService($redis);
    }

    public function validatePassport(): void
    {

        if (empty($_REQUEST['series']) || empty($_REQUEST['number'])) {
            $this->exitWithBadRequestError("Parameters 'series' and 'number' should not be empty");
        }
        $series = $_REQUEST['series'];
        $number = $_REQUEST['number'];

        try {
            $isValid = $this->invalidPassportsService->isValid($series, $number);
            if ($isValid) {
                $validityString = "valid";
            } else {
                $validityString = "invalid";
            }

            header('Content-Type: application/json');
            echo '{"result":"' . $validityString . '"}';
        } catch (BadRequestException $exception) {
            $this->exitWithBadRequestError( $exception->getMessage());
        }
    }

    protected function exitWithBadRequestError(string $errorMessage): void
    {
        header("HTTP/1.1 400 Bad request");
        header('Content-Type: application/json');
        echo '{"error":"' . $errorMessage . '"}';
        exit;
    }
}
