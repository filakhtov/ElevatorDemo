<?php namespace Lender411;

use \Lender411\Elevator;

class DoorCloseSignalProcessor extends SignalProcessor
{

    public function handleSignal(Elevator $elevator, $signal)
    {
        if ($signal === Elevator::STATE_DOOR_CLOSE) {
            if ($elevator->isDoorOpen()) {
                $state = Elevator::STATE_STANDING;
            } else {
                $state = NULL;
            }
        } else {
            $state = $this->delegate($elevator, $signal);
        }

        return $state;
    }

}
