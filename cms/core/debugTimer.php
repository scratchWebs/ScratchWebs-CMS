<?php

class debugTimer
{
	public $startTime;
	public $endTime;
	public $totalTime;
	
	public function __construct()
	{
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$this->startTime = $mtime;
	}
	public function stop()
	{
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$this->endTime = $mtime;
		$this->totalTime = ($this->endTime - $this->startTime);
	}
	public function getRunTime()
	{
		$this->stop();
		return "This page was created in " . $this->totalTime . " seconds.";	
	}
}

?>