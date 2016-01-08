<?php namespace Lender411\Controller;

use \Lender411\ElevatorRepository;
use \Exception;
use \Lender411\Elevator;
use \Lender411\ElevatorRequest;

class ElevatorController
{

    /** @var ElevatorRepository */
    private $elevatorRepository;

    /** @var Elevator */
    private $elevator;

    public function getInfoAction()
    {
        $this->elevatorRepository = new ElevatorRepository("data/state");
        $this->elevator = $this->elevatorRepository->getElevator();

        try {
            $this->elevator->process();
            $this->elevatorRepository->saveElevator($this->elevator);
        } catch (Exception $e) {

        }

        $info = new \Lender411\ElevatorInfo($this->elevator);
        return json_encode(["description" => $info->getState(), "currentFloor" => $this->elevator->getCurrentFloor(), "targetFloor" => $this->elevator->getTargetFloor(), "state" => $this->elevator->getState()]);
    }

    public function addRequestAction($request)
    {
        $this->elevatorRepository = new ElevatorRepository("data/state");
        $this->elevator = $this->elevatorRepository->getElevator();

        $this->elevator->addRequest(new ElevatorRequest($request->from, $request->to));
        $this->elevatorRepository->saveElevator($this->elevator);

        return json_encode(["success" => "Request successfully received."]);
    }

}
