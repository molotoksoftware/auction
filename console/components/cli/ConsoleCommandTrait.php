<?php

/**
 * Class ConsoleCommandTrait
 */
trait ConsoleCommandTrait
{
    private $startTime;
    private $endTime;
    private $executionTime;

    public function startTimeTracking()
    {
        $this->startTime = microtime(true);
        $this->endTime = null;
        $this->executionTime = null;
        $this->log('Begin time tracking.');
    }

    public function stopTimeTracking()
    {
        $this->endTime = microtime(true);
        $this->executionTime = $this->endTime - $this->startTime;
        $this->log('Complete time tracking.');
    }

    public function showTime()
    {
        $time = gmdate("H:i:s", $this->executionTime);
        $this->log(sprintf("Time execution is %s.", $time));
    }

    public function log($msg)
    {
        echo sprintf("[%s] %s", date('H:i:s'), $msg) . "\n";
    }
}