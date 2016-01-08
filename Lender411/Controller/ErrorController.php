<?php namespace Lender411\Controller;

use \Exception;

class ErrorController
{
    public function exceptionAction(Exception $e)
    {
        http_response_code($e->getCode());
        return json_encode(["error" => $e->getMessage()]);
    }

}
