<?php namespace Lender411;

use \Lender411\Elevator;
use \Exception;

class AlarmSignalProcessor extends SignalProcessor
{

    public function handleSignal(Elevator $elevator, $signal)
    {
        if ($signal === Elevator::STATE_ALARM) {
            if ($elevator->isDoorOpen()) {
                throw new Exception("Elevator has door open, why should you push an alarm button?");
            } else {
                $state = Elevator::STATE_ALARM;
            }
        } else {
            $state = $this->delegate($elevator, $signal);
        }

        return $state;
    }

}
