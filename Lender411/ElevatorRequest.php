<?php namespace Lender411;

use \Exception;

class ElevatorRequest
{

    private $sourceFloor;
    private $targetFloor;

    public function __construct($source, $target)
    {
        if (!is_int($source)) {
            throw new Exception("Invalid source floor value: Integer expected, got: " . print_r($source));
        }

        if (!is_int($target)) {
            throw new Exception("Invalid target floor value: Integer expected, got: " . print_r($target));
        }

        $this->sourceFloor = $source;
        $this->targetFloor = $target;
    }

    public function getSourceFloor()
    {
        return $this->sourceFloor;
    }

    public function getTargetFloor()
    {
        return $this->targetFloor;
    }

}
