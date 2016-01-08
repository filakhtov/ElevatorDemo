<?php namespace Lender411\Controller;

use \stdClass;
use \Lender411\Exception\BadRequestException;
use \Lender411\ElevatorRepository;

class SignalController
{
    public function sendSignalAction(stdClass $request)
    {
        if (!isset($request->signal)) {
            throw new BadRequestException("Signal not given");
        }

        $elevatorRepository = new ElevatorRepository('data/state');
        $elevator = $elevatorRepository->getElevator();
        $elevator->sendSignal($request->signal);
        $elevatorRepository->saveElevator($elevator);

        return json_encode(["success" => "Signal successfully handled."]);
    }

}
