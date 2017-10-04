<?php
class NMEA
{
 	private $fields;
	private $checksum;
	private $sentence;
	
	public function setSentence($sentence)
	{	
	    $this->sentence = $sentence;
		$parts = explode('*', $sentence);
		$this->checksum = count($parts) > 1 ? $parts[1] : null;
		$this->fields = explode(',', $parts[0]);
	}
	
	public function isValid()
	{
		return $checksum == 'ff';
	}
	
	public function getHeader()
	{
		return $this->fields[0];
	}
	
	public function getField($index)
	{
		return count($this->fields) > $index + 1 ? $this->fields[$index+1] : false;
	}
	
	public function getSentence()
	{
		return $this->sentence;
	}
}
?>