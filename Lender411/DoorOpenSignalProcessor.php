<?php namespace Lender411;

use \Exception;
use \Lender411\Elevator;

class DoorOpenSignalProcessor extends SignalProcessor
{

    public function handleSignal(Elevator $elevator, $signal)
    {
        if ($signal === Elevator::STATE_DOOR_OPEN) {
            if ($elevator->isDoorOpen() || $elevator->isStanding()) {
                $state = Elevator::STATE_DOOR_OPEN;
            } else {
                throw new Exception("Elevator is on it's way. Can not open door right now, sorry!");
            }
        } else {
            $state = $this->delegate($elevator, $signal);
        }

        return $state;
    }

}
