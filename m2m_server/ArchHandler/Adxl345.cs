/*
 * Created by SharpDevelop.
 * User: stefan
 * Date: 19.11.2015
 * Time: 19:40
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;

namespace ArchHandler
{
	/// <summary>
	/// Description of Adxl345.
	/// </summary>
	public class Adxl345
	{
		double roll;
		double pitch;
		CliCall reader;
		
		public Adxl345()
		{
			//XXX
			//this.reader = new CliCall("/bin/python2", "/root/script/adxl345.py");
			
		}
		
		public void readRollAndPitch()
		{
			
			try{
				//XXX fix 24.9.2017 to get temp working in raspbian
				//string result = this.reader.execute();
				//string[] lines = result.Split(Environment.NewLine.ToCharArray());
				this.roll = 0; //Convert.ToDouble(lines[0].Substring(lines[0].IndexOf(':')+1));
				this.pitch = 0; //Convert.ToDouble(lines[1].Substring(lines[1].IndexOf(':')+1));
			}
			catch (FormatException)
			{
				this.roll = -99;
				this.pitch = -99;
			}
		}
		
		public double getRoll()
		{
			return this.roll;
		}
		
		public double getPitch()
		{
			return this.pitch;
		}
			
	}
}
