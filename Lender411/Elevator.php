<?php namespace Lender411;

use \Lender411\FloorService;
use \Lender411\ElevatorRequest;
use \Lender411\QueueService;
use \Lender411\SignalProcessor;
use \Exception;

class Elevator
{

    const STATE_DURATION = 3;

    const STATE_STANDING = 0;
    const STATE_MOVING_UP = 1;
    const STATE_MOVING_DOWN = -1;
    const STATE_DOOR_OPEN = 2;
    const STATE_ALARM = 3;
    const STATE_DOOR_CLOSE = 4;

    /** @var int */
    private $state = self::STATE_STANDING;

    /** @var array */
    private $queue = [];

    /** @var int */
    private $currentFloor;

    /** @var int */
    private $targetFloor;

    /** @var int */
    private $timestamp;

    /** @var QueueService */
    private $queueService;

    /** @var FloorService */
    private $floorService;

    /** @var SignalProcessor */
    private $signalProcessor;

    public function __construct(QueueService $queueService, FloorService $floorService, SignalProcessor $signalProcessor)
    {
        $this->currentFloor = 1;
        $this->queueService = $queueService;
        $this->floorService = $floorService;
        $this->signalProcessor = $signalProcessor;
        $this->resetTimeStamp();
    }

    /** @return Elevator */
    public function sendSignal($signal)
    {
        $this->process();
        $state = $this->signalProcessor->handleSignal($this, $signal);

        if (!is_null($state)) {
            $this->state = $state;
            $this->resetTimeStamp();
        }

        return $this;
    }

    /** @return Elevator */
    public function addRequest(ElevatorRequest $request)
    {
        $this->process();

        if (!$this->floorService->getFloorState($request->getSourceFloor())) {
            throw new Exception("Source floor is under maintenance.");
        }

        if (!$this->floorService->getFloorState($request->getTargetFloor())) {
            throw new Exception("Target floor is under maintenance.");
        }

        $this->queue = $this->queueService->addRequest($this->queue, $this, $request);
        $this->targetFloor = array_shift($this->queue);

        return $this;
    }

    /** @return Elevator */
    public function process()
    {
        $this->checkAlarmState()
                ->checkDoor()
                ->getNextRequest();

        if ($this->hasCurrentRequest() && !$this->isDoorOpen()) {
            $this->moveElevator()
                    ->checkIfRequestSatisfied();
        }

        return $this;
    }

    /** @return Elevator */
    private function checkIfRequestSatisfied()
    {
        if ($this->currentFloor === $this->targetFloor) {
            $this->state = self::STATE_DOOR_OPEN;
            $this->targetFloor = null;
            $this->updateTimestamp();
        }

        return $this;
    }

    /** @return Elevator */
    private function getNextRequest()
    {
        if (!$this->hasCurrentRequest() && !$this->isDoorOpen()) {
            $this->targetFloor = array_shift($this->queue);
            $this->resetTimeStamp();
        }

        return $this;
    }

    /** @return Elevator */
    private function checkAlarmState()
    {
        if ($this->state === self::STATE_ALARM) {
            throw new Exception("Elevator is in alarm state. No requests will be handled.");
        }

        return $this;
    }

    /** @return Elevator */
    private function moveElevator()
    {
        switch (true) {
            case $this->targetFloor < $this->currentFloor:
                $this->state = self::STATE_MOVING_DOWN;
                $go = -1;
                break;

            case $this->targetFloor > $this->currentFloor:
                $this->state = self::STATE_MOVING_UP;
                $go = 1;
                break;
        }

        if ($this->isReadyForNextAction()) {
            $this->currentFloor += $go;
            $this->updateTimestamp();
        }

        return $this;
    }

    /** @return Elevator */
    private function checkDoor()
    {
        if ($this->isDoorOpen()) {
            if ($this->isReadyForNextAction()) {
                $this->state = self::STATE_STANDING;
                $this->updateTimestamp();
            }
        }

        return $this;
    }

    /** @return bool */
    private function isReadyForNextAction()
    {
        return ($this->timestamp + self::STATE_DURATION) <= time();
    }

    /** @return Elevator */
    private function updateTimestamp()
    {
        $this->timestamp += self::STATE_DURATION;
        return $this;
    }

    private function resetTimeStamp()
    {
        $this->timestamp = time();
        return $this;
    }

    /** @return int */
    public function getState()
    {
        return $this->state;
    }

    /** @return bool */
    public function hasCurrentRequest()
    {
        return !is_null($this->targetFloor);
    }

    /** @return bool */
    public function isDoorOpen()
    {
        return $this->state === self::STATE_DOOR_OPEN;
    }

    /** @return bool */
    public function isStanding()
    {
        return $this->state === self::STATE_STANDING;
    }

    /** @return int */
    public function getCurrentFloor()
    {
        return $this->currentFloor;
    }

    /** @return int */
    public function getTargetFloor()
    {
        return $this->targetFloor;
    }

}
