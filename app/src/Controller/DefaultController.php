<?php

namespace App\Controller;

use App\Service\InvalidPassportsServiceInterface;

class DefaultController
{
    /** @var InvalidPassportsServiceInterface */
    protected $invalidPassportsService;

    public function __construct(InvalidPassportsServiceInterface $service)
    {
        $this->invalidPassportsService = $service;
    }

    public function validatePassport(): void
    {

        if (empty($_REQUEST['series']) || empty($_REQUEST['number'])) {
            $this->exitWithBadRequestError("Parameters 'series' and 'number' should not be empty");
        }
        $series = $_REQUEST['series'];
        $number = $_REQUEST['number'];

        if (strlen($series) !== 4) {
            $this->exitWithBadRequestError("Length of series must be 4");
        }
        if (strlen($number) !== 6) {
            $this->exitWithBadRequestError("Length of number must be 6");
        }
        if (!is_numeric($series) || !is_numeric($number)) {
            $this->exitWithBadRequestError("Values of series and number should be numeric");
        }

        try {
            $isValid = $this->invalidPassportsService->isValid($series, $number);
            if ($isValid) {
                $validityString = "valid";
            } else {
                $validityString = "invalid";
            }

            header('Content-Type: application/json');
            echo '{"result":"' . $validityString . '"}';
        } catch (\Throwable $exception) {
            error_log($exception);
            header("HTTP/1.1 500 Internal Server Error");
            header('Content-Type: application/json');
            echo '{"error":"' . $exception->getMessage() . '"}';
        }
    }

    public function resetCache()
    {
        opcache_reset();
        echo 'OK';
    }

    public function prepareCache()
    {
        $this->invalidPassportsService->isValid(1111, 223344);
        echo 'OK';
    }

    protected function exitWithBadRequestError(string $errorMessage): void
    {
        header("HTTP/1.1 400 Bad request");
        header('Content-Type: application/json');
        echo '{"error":"' . $errorMessage . '"}';
        exit;
    }
}
