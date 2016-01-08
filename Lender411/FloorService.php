<?php namespace Lender411;

use \Exception;

class FloorService
{

    private $floorStates;

    public function __construct(array $floorStates)
    {
        $this->floorStates = $floorStates;
        ksort($this->floorStates);
        $this->validateFloorStates();
    }

    private function validateFloorStates()
    {
        $i = 1;
        foreach ($this->floorStates as $floor => $floorState) {
            if ($floor !== $i++) {
                throw new Exception("Inconsistent floor states input.");
            }

            if (!is_bool($floorState)) {
                throw new Exception("Floor status must be bool. Received: " . PHP_EOL . print_r($floorState, true));
            }
        }
    }

    public function getFloorState($floor)
    {
        if (!in_array($floor, $this->floorStates)) {
            throw new Exception("Unknown floor status requested: {$floor}");
        }

        return $this->floorStates[$floor];
    }

    public function getFloorsStatus()
    {
        return $this->floorStates;
    }

}
