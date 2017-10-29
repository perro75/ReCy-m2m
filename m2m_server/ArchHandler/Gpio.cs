/*
 * Created by SharpDevelop.
 * User: stefan
 * Date: 17.11.2015
 * Time: 22:07
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;

namespace ArchHandler
{
	/// <summary>
	/// Description of Gpio.
	/// </summary>
	public class Gpio
	{
		CliCall OnCall, OffCall, ReadCall;
		int pinValue = -1;
		pintype type;
		int number;
		
		public enum pintype {INPUT, OUTPUT}
		
		// print table of alla GPIO
		public static string debug()
		{
			return new CliCall("gpio", "readall").execute();
		}
		
		public Gpio(int number, pintype type)
		{
			 this.OnCall = new CliCall("gpio", "-g write "+number+" 1");
			 this.OffCall = new CliCall("gpio", "-g write "+number+" 0");
			 this.ReadCall = new CliCall("gpio", "-g read " + number);
			
			 this. type = type;
			 this.number = number;
			 
			//export pin as input or output
			if (type == pintype.OUTPUT)
			{
				new CliCall("gpio", "export "+number+" output").execute();
				this.Off();
			}
			else //input pin, pull up
			{
				new CliCall("gpio", "export " + number + " input").execute();
				new CliCall("gpio", "- g mode " + number + " up").execute();
			}
		}
		
		private bool readPin()
		{
			this.pinValue = Convert.ToInt32(ReadCall.execute());
			return this.pinValue == 1;
		}
		
		public bool isOn()
		{
			//read pin if input
			if (this.type == pintype.INPUT)
				return this.readPin();
			
			return this.pinValue == 1;
		}
		
		public string On()
		{
			this.pinValue = 1;
			return this.OnCall.execute();
		}
		
		public string Off()
		{
			this.pinValue = 0;
			return this.OffCall.execute();
		}
		
		public string Toggle()
		{
		
			return (this.pinValue == 0 ) ? this.On() : this.Off();
		}
		
	}
}
