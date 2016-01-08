<?php namespace Lender411;

use \Lender411\Elevator;

class ElevatorInfo
{

    private $elevator;

    public function __construct(Elevator $elevator)
    {
        $this->elevator = $elevator;
    }

    public function getState()
    {
        switch ($this->elevator->getState()) {
            case Elevator::STATE_ALARM:
                $description = "Elevator is between {$this->elevator->getCurrentFloor()} and {$this->elevator->getTargetFloor()} in alarm state.";
                break;

            case Elevator::STATE_DOOR_OPEN:
                $description = "Door is open at {$this->elevator->getCurrentFloor()} floor";
                break;

            case Elevator::STATE_STANDING:
                $description = "Elevator is standing at {$this->elevator->getCurrentFloor()} floor";
                break;

            case Elevator::STATE_MOVING_DOWN:
                $description = "Elevator is moving down to {$this->elevator->getTargetFloor()} floor";
                break;

            case Elevator::STATE_MOVING_UP:
                $description = "Elevator is moving up to {$this->elevator->getTargetFloor()} floor";
                break;
        }

        return $description;
    }

}
