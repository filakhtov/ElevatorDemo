<?php namespace Lender411;

use \Exception;

class Logger
{

    private $file;

    public function __construct($logfile)
    {
        if (file_exists($logfile)) {
            if (!is_file($logfile)) {
                throw new Exception("{$logfile}: Is not a regular file");
            } elseif (!is_writable($logfile)) {
                throw new Exception("{$logfile}: Unable to write. Permission denied.");
            }
        } else {
            $dir = dirname($logfile);
            if (!is_dir($dir)) {
                throw new Exception("{$logfile}: Unable to create log file. No such folder.");
            } elseif (!is_writable($dir) || !is_readable($dir)) {
                throw new Exception("{$logfile}: Unable to create log file. Permission denied.");
            }
        }

        $this->file = $logfile;
    }

    public function __invoke($data)
    {
        $ts = time("Y-m-d H:i:s");
        file_put_contents($this->file, "[{$ts}] {$data}" . PHP_EOL, FILE_APPEND);
    }

}
