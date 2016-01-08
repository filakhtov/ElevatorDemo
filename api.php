<?php
require_once 'autoloader.php';

use \Lender411\Exception\BadRequestException;
use \Lender411\Controller\ErrorController;
use \Lender411\Controller\ElevatorController;
use \Lender411\Controller\SignalController;
use \Lender411\Controller\ResetStateController;
use \Lender411\Controller\FloorsController;
use \Lender411\Logger;

$request = filter_input(INPUT_SERVER, "REQUEST_URI");
$method = filter_input(INPUT_SERVER, "REQUEST_METHOD");
$requestJson = file_get_contents("php://input");

$logger = new Logger("data/api.log");

try {
    if ($method !== "GET") {
        $requestData = json_decode($requestJson);

        if (is_null($requestData)) {
            throw new BadRequestException("Bad request payload");
        }
    }

    switch ($request) {
        case "/api.php/elevator":
            $controller = new ElevatorController;

            if ($method === "POST") {
                $response = $controller->addRequestAction($requestData);
            } elseif ($method === "GET") {
                $response = $controller->getInfoAction();
            } else {
                throw new BadRequestException("Bad method");
            }
            break;

        case "/api.php/signal":
            if ($method === "POST") {
                $controller = new SignalController;
                $response = $controller->sendSignalAction($requestData);
            } else {
                throw new BadRequestException("Bad method");
            }
            break;

        case "/api.php/floors":
            if ($method === "GET") {
                $controller = new FloorsController;
                $response = $controller->getFloorsAction();
            } else {
                throw new BadRequestException("Bad method");
            }
            break;

        case "/api.php/reset":
            if ($method === "POST") {
                $controller = new ResetStateController;
                $controller->resetAction();
            } else {
                throw new BadRequestException("Bad method");
            }
            break;

        default:
            throw new BadRequestException("Not found");
        // no break
    }

} catch (BadRequestException $e) {
    $controller = new ErrorController();
    $response = $controller->exceptionAction($e);
} catch (Exception $e) {
    $controller = new ErrorController();
    $badRequest = new BadRequestException($e->getMessage(), 400, $e);
    $response = $controller->exceptionAction($badRequest);
}

$logger("Request: {$method} {$request} {$requestJson}; Response: {$response}");

echo $response;
