<?php
//HexCode class handles bits in a HEX-char string.
//FIXME::HexCode must be 4-chars for now (integer size & sprintf coding )
//With a string of 4 hexchars, everythin seem to work fine.
//Same file is ported also for Arduino


if (isset($_GET['DEBUG']))
{
	echo @"
	\$hx = new HexCode(); //create object<br>
	\$hx->begin('FFFF'); //use any number of hexcahars that fit into an integer<br>
	\$hx->toggleBit(0); //toggle bit<br>
	\$hx->clearBit(1); // set bit<br>
	\$hx->setBit(2);  // clear bit<br>
	\$hx->readBit(5); // returns 0 or 1. In case of a problem -1<br>
	\$hx->readHex(); // return the hex-string<br>
	\$hx->readInt(); // return integer. In case of a problem -1<br>
	\$hx->bitCount(); // returns number of bits (chars * 4)<br>
	<br>
	";
	
	//TEST
	$hx = new HexCode();

	$hx->begin('FT'); // NON HEX
	echo "<li>";
	echo $hx->readHex(); // -1
	echo $hx->readBit(0); // -1
	echo $hx->readInt(); // -1

	echo "</li>";

	$hx->begin('0001'); // 1111 1111 1111 1111

	for ($row =0; $row < 32; $row++)
	{
		echo "<li>".$hx->readHex()." | ".$hx->readInt();
		for ($i=0; $i < $hx->bitCount(); $i++)
		{
			echo " | ". $hx->readBit($i); 
		}
		echo "</li>";
		
		$hx->toggleBit(0);
		$hx->clearBit(1);
		
		$hx->toggleBit(2);
		$hx->setBit(2);
		
		$hx->toggleBit(15);
		
		if ($row > 16)
		{
			$hx->clearBit(5);
			$hx->clearBit(6);
			$hx->clearBit(7);
			$hx->clearBit(8);
		}
	}
}

//Class handles bitwise data stored as HEX-charstring
class HexCode
{

	private $hh; // the variable-length hex-charstring
	
	//in arduino constructor does not use parameters.
	function begin($hh)
	{
		$this->hh = $hh;
	}
	
	//set bit
	function setBit($n)
	{
		if ( $this->readBit($n) == 0 ) $this->toggleBit($n);
	}
	
	//clear bit
	function clearBit($n)
	{
		if ( $this->readBit($n) == 1 ) $this->toggleBit($n);
	}
	
	//toggle bit an return result as a copy of HEx
	function toggleBitCopy($n)
	{
		//read string to integer
		$dec = $this->hex2int();
		
		$dec = $dec ^= 1 << $n;
		return sprintf("%04X", $dec);
	}
	
	//toggle bit
	function toggleBit($n)
	{
		//read string to integer
		$dec = $this->hex2int();
		
		$dec = $dec ^= 1 << $n;
		$this->hh = sprintf("%04X", $dec);
	}
	
	//read the bit value from given hex-ascii string
	function readBit($n)
	{

		$dec = $this->hex2int();
		
		//Make bitshift to INT 00000001 and use AND
		//if result is 0 then zero. if reslut > zero (2,4,8...) then 1
		if ( $dec < 0 ) return -1;
		if ( $n >= $this->bitCount() ) return -1;
		return  ( $dec & ( 1 << $n) ) > 0 ? 1 : 0;
	}
	
	//return data as HEX-string
	function readHex()
	{
		return $this->hh;
	}
	
	//return data as integer
	function readInt()
	{
		return $this->hex2int();
	}
	
	//Cuunt the bits in data as characters * 4
	function bitCount()
	{
		return strlen($this->hh) * 4;
	}
	
	//convert any number of HEX-chars
	private function hex2int()
	{
		$dec = 0;
		$len = strlen($this->hh);
		
		for ($i = 0; $i < $len; $i++)
		{
			//return -1 if non-hex char
			$val = $this->hexchar2int($this->hh[$len - $i -1]);
			if ($val < 0) return -1;
			
			//read the values in reverse order ( [0] is leftmost in String )
			$dec += ( $val ) << ( 4 * $i);
		}
		return $dec;
	}

	//Convert one HEX char 0..9 A..F a..f to integer 0...15
	private function hexchar2int ($h)
	{
		//ascii value of char
		$c = ord($h);
		  
		if ($h >= '0' && $h <= '9')
			return $c - ord('0');
		if ($h >= 'A' && $h <= 'F')
			return $c - ord('A')  + 10;
		if ($h >= 'a' && $h <= 'f')
			return $c - ord('a') + 10;
		
		//no hex
		return -1;
	}
}
?>