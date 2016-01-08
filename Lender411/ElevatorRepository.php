<?php namespace Lender411;

use \Exception;
use \Lender411\QueueService;
use \Lender411\Elevator;
use \Lender411\FloorService;
use \Lender411\AlarmSignalProcessor;
use \Lender411\DoorCloseSignalProcessor;
use \Lender411\DoorOpenSignalProcessor;

class ElevatorRepository
{

    private $file;
    private $floorService;

    public function __construct($file)
    {
        if (is_file($file) && !is_readable($file)) {
            throw new Exception("{$file}: Unable to read state file, permission denied.");
        }

        if (file_exists($file) && !is_file($file)) {
            throw new Exception("{$file}: Inavlid state file, not a regular file.");
        } else {
            if (!@touch($file)) {
                throw new Exception("{$file}: Unable to create state file.");
            }
        }

        $this->file = $file;
    }

    /** @return FloorService */
    public function getFloorsService()
    {
        if (is_null($this->floorService)) {
            $this->floorService = new FloorService([
                1 => true,
                false,
                true,
                false,
                true,
                true,
                true,
                true,
                true,
                true,
                true,
                true,
                true,
                true,
                true
            ]);
        }
        return $this->floorService;
    }

    /** @return Elevator */
    public function getElevator()
    {
        $elevator = $this->restoreElevatorStatus();

        if ($elevator === FALSE) {
            $floorService = $this->getFloorsService();
            $queueService = new QueueService();
            $signalProcessor = new AlarmSignalProcessor(new DoorOpenSignalProcessor(new DoorCloseSignalProcessor));
            $elevator = new Elevator($queueService, $floorService, $signalProcessor);
        }

        return $elevator;
    }

    private function restoreElevatorStatus()
    {
        $data = file_get_contents($this->file);
        $elevator = @unserialize($data);
        return $elevator;
    }

    public function saveElevator(Elevator $elevator)
    {
        file_put_contents($this->file, serialize($elevator));
    }

}
