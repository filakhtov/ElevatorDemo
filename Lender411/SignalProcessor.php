<?php namespace Lender411;

use \Exception;
use \Lender411\Elevator;

abstract class SignalProcessor
{

    /** @var SignalProcessor */
    private $nextProcessor;

    final public function __construct(SignalProcessor $signalHandler = null)
    {
        $this->nextProcessor = $signalHandler;
    }

    abstract public function handleSignal(Elevator $elevator, $signal);

    final public function delegate(Elevator $elevator, $signal)
    {
        if (is_null($this->nextProcessor)) {
            throw new Exception("Unrecognized signal received: " . print_r($signal));
        }

        return $this->nextProcessor->handleSignal($elevator, $signal);
    }

}
