<?php

class AnalogSensorSet
{
	private $analogSensors;
	
	function __construct($analogSensors)
    {
        $this->analogSensors = $analogSensors;
    }
	
	function getSensors()
	{
		return $this->analogSensors;
	}
    
	function getGroups()
	{
		$groups = array();
		foreach($this->analogSensors as $a)
		{
			$g = $a->getGroup();
			$groups[$g] = $g;
		}
		ksort($groups);
		return $groups;
	}
	
	function getSensorsInGroup($groupName)
	{
		$sensors = array();
		foreach($this->analogSensors as $a)
		{
			$g = $a->getGroup();
			if ($g == $groupName)
			$sensors[$a->getWeight()] = $a;
		}
		ksort($sensors);
		return $sensors;
	}
	
	
}

class AnalogSensor
{
    
    private $layer, $number, $group, $weight, $name, $type, $high, $low, $value, $unit, $age;
    
    function __construct($laynum, $ainum)
    {
        $this->layer = $laynum;
        $this->number = $ainum;
		
    }
    
	function getLayer()
	{
		return $this->layer;
	}
	
	function getNumber()
	{
		return $this->number;
	}
	
	function getName()
	{
		return $this->name;
	}
	
	function getGroup()
	{
		return $this->group;
	}
	
	function getWeight()
	{
		return $this->weight;
	}
	
    function getValue()
    {
        return $this->value;
    }
	
	function getHighLimit()
    {
        return $this->high;
    }
	
	function getLowLimit()
    {
        return $this->low;
    }
	
	function getDataAge()
	{
		return $this->age;
	}
	
	function isMatch($layer, $number)
	{
		return ($this->layer == $layer && $this->number == $number);
	}
	
	function sensorType()
	{
		return $this->type;
	}
	
	function getLastHourHistory()
	{
		$hist = new AnalogHistory();
		$hist->readHistory($this->layer, $this->number, 60*12);
		return $hist;
	}
	
	function setDefinedData($group, $weight, $name, $type, $high, $low)
    {
        $this->group = $group;
		$this->weight = $weight;
        $this->name = $name;
        $this->type = $type;
        $this->high = $high;
        $this->low = $low;
    }
    
	function setDataAge($age)
	{
		$this->age = $age;
	}
	
    function setValue($value, $unit = null)
    {
        $this->value = $value;
		$this->unit = $unit;
    }
	
	function debug()
	{
		echo "<li>$this->name: $this->value $this->unit";
		echo "MIN: ".$this->history->getMin();	
		echo "AVG: ".$this->history->getAvg();	
		echo "MAX: ".$this->history->getMax();
		echo "FIRST: ".$this->history->getFirst();	
		echo "LAST: ".$this->history->getLast();
		echo "CHANGE: ".$this->history->getChange();		
	}
 
}

?>