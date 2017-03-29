<?php

interface I_TLSP_JOB
{
	public function userCheckout($uid);
	public function sysRunJob();
}

class TLSPJob implements I_TLSP_JOB
{
	private $uid = 0;
	private $squeue = null;
	
	public function __construct($uid = 0, $squeue)
	{
		$this->uid = $uid;
		$this->squeue = $squeue;
	}
	
	public function userCheckout($uid)
	{
		if (!$this->inWaitingQ($uid))
		{
			$this->pushToWaitingQ($uid);
		}
		else if ($this->readyRunningQ($uid))
		{
			$this->pushToRunningQ($uid);
		}
		else if ($this->inRunningQ($uid))
		{
		}
	}
	
	public function sysRunJob()
	{
		if (($uid = $this->popRunningQ()))
		{
			$this->sysRun($uid);
		}
	}
	
	private function sysRun($uid)
	{
		return true;
	}
	
	private function pushToWaitingQ($uid)
	{
	}
	
	private function pushToRunningQ($uid)
	{
	}
	
	private function inWaitingQ($uid)
	{
	}
	
	private function isReadyRunningQ($uid)
	{
		return !$this->inRunningQ($uid) && !$this->isFullRunningQ($uid);
	}
	
	private function inRunningQ($uid)
	{
	}
	
	private function isFullRunningQ($uid)
	{
	}
}