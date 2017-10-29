/*
 * Created by SharpDevelop.
 * User: stefan
 * Date: 17.11.2015
 * Time: 22:50
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.Collections;

namespace ArchHandler
{
	/// <summary>
	/// Description of HexCode.
	/// </summary>
	public class HexCode
	{
		private bool[] bits = new bool[4];
		private string hex = null;
		
		public HexCode(string hex)
		{
			int number = int.Parse(hex, System.Globalization.NumberStyles.HexNumber);
			
			//Console.WriteLine(number.ToString());
			
			int number2 = int.Parse("000F", System.Globalization.NumberStyles.HexNumber);
			
			for (int bitNumber = 0; bitNumber < 4; bitNumber++)
			{
				this.bits[bitNumber] = (number & (1 << bitNumber)) != 0;
				//Console.WriteLine(bits[bitNumber].ToString());
				
				//clear bit is not set
				if (!this.bits[bitNumber])
					number2 = number2 &= ~(1 << bitNumber);
			}
			
			//string hexValue = number.ToString("X4");
			
			this.hex = number2.ToString("X4");
			
		}
		
		public bool[] getBits()
		{
			return this.bits;
		}
		
		public string getHex()
		{
			return this.hex;
		}
		
	}
}
