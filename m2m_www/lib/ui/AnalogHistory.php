<?php

class AnalogHistory
{
	private $min = array();
	private $avg = array();
	private $max = array();
	
	function __construct()
	{
		
	}
	
	function readHistory($layer, $number, $limit)
	{
		//FIXME
		global $db;
		$sql = getAranaHistoryByMinute($layer, $number, $limit);
		$res = $db->query($sql);
		//$res->setFetchMode(PDO::FETCH_NUM);
		
		foreach($res as $row)
		{
			$this->min[] = (float)$row['min'];
			$this->avg[] = (float)$row['avg'];
			$this->max[] = (float)$row['max'];
		}
	}
	
	function getChange($withSign = false)
	{
		$change = round($this->getFirst() - $this->getLast(),1);
		
		if ($withSign)
		{
			return $change > 0 ? "+$change" : $change;
		}
		
		return $change;
	}
	
	function getFirst()
	{
		return $this->avg[0];
	}
	
	function getLast()
	{
		return end($this->avg);
	}
	
	function getAvg()
	{
		return array_sum($this->avg) / count($this->avg);
	}
	
	function getMin()
	{
		return min($this->avg);
	}
	
	function getMax()
	{
		return max($this->avg);
	}
	
	function printHistory()
	{
		echo implode('|', $this->avg);		
	}
	
}

?>