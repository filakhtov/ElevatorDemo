<?php namespace Lender411\Controller;

class ResetStateController
{
    public function resetAction()
    {
        if (file_exists('data/state')) {
            // Truncate state and log files
            foreach (['data/state', 'data/api.log'] as $file) {
                $f = fopen($file, 'w');
                fclose($f);
            }
        }

        return json_encode(["success" => "State was successfully reset."]);
    }

}
