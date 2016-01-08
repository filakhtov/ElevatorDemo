<?php namespace Lender411;

use \Lender411\Elevator;
use \Lender411\ElevatorRequest;

class QueueService
{

    /** @var array */
    private $queue;

    /** @var Elevator */
    private $elevator;

    /** @return array */
    public function addRequest(array $queue, Elevator $elevator, ElevatorRequest $request)
    {
        $this->queue = $queue;
        $this->elevator = $elevator;

        // Insert request which is currently handled by elevator (if any) into queue
        if ($elevator->hasCurrentRequest()) {
            array_unshift($this->queue, $elevator->getTargetFloor());
        }

        if ($request->getSourceFloor() === $request->getTargetFloor()) {
            // Ignore request from same floor
        } else if (count($this->queue) === 0) {
            // If queue is empty, push requests directly into queue
            $this->queue = [$request->getSourceFloor(), $request->getTargetFloor()];
        } else {
            // If queue has some elements - put request into most appropriate place
            $this->route($request);
        }

        return $this->queue;
    }

    /** @retrun QueueService */
    private function route(ElevatorRequest $request)
    {
        // Insert current elevator floor as a starting point
        array_unshift($this->queue, $this->elevator->getCurrentFloor());

        // Find the best place and insert source request (where to pick up)
        $position = $this->insertRequest(0, $request->getSourceFloor());
        // Find the best place after picking-up to land
        $this->insertRequest($position, $request->getTargetFloor());

        // Remove current elevator floor after successful routing
        array_shift($this->queue);

        return $this;
    }

    /** Function searches for best possible place to insert floor where to stop
     * @return int position in the queue at which request was placed */
    private function insertRequest($startAt, $floor)
    {
        $alreadyInQueue = array_search($floor, $this->queue);
        if ($alreadyInQueue !== FALSE) {
            return $alreadyInQueue;
        }

        while ($startAt + 1 < count($this->queue)) {
            $sourceFloor = $this->queue[$startAt];
            $targetFloor = $this->queue[++$startAt];

            if ($this->isFloorBetween($floor, $sourceFloor, $targetFloor)) {
                array_splice($this->queue, $startAt, 0, $floor);
                break;
            }
        }

        if ($startAt + 1 === count($this->queue)) {
            $this->queue[] = $floor;
        }

        return $startAt;
    }

    /** @return bool if target floor is between $a and $b */
    private function isFloorBetween($floor, $a, $b)
    {
        $min = min($a, $b);
        $max = max($a, $b);

        return $min < $floor && $max > $floor;
    }

}
