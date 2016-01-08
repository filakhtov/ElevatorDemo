<?php namespace Lender411\Controller;

use \Lender411\ElevatorRepository;

class FloorsController
{

    public function getFloorsAction()
    {
        $elevatorRepository = new ElevatorRepository('data/state');
        $floorService = $elevatorRepository->getFloorsService();
        $status = $floorService->getFloorsStatus();

        return json_encode(["floorsCount" => count($status), "floorsStatus" => $status]);
    }

}
