<?php namespace Lender411\Exception;

use \Exception;

class BadRequestException extends Exception
{

    public function __construct($message, $code = 400, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
