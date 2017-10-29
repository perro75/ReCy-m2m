/*
 m2m- server software for reading 1-wire temps and sending data to remove server over http.
*/
using System;

namespace ArchHandler
{
	class Program
	{
		public static void Main(string[] args)
		{
			//use inifile for storing data
			var iniFile = new IniGroupFileRW("/root/script/w1_ini");
			//The URL where to send data
			string url = iniFile.getValue("URL");
			//The installation id
			string uid = iniFile.getValue("UID");
			//layers default is MUX (8)
			int layers = Convert.ToInt32(iniFile.getValue("LAYERS"));
			//layer offset to use in NMEA (simply added to each layer like 1+10 = 11)
			int layerOffset = Convert.ToInt32(iniFile.getValue("LAYER_OFFSET"));
			
			// default number of layers is 8			
			if (layers == 0)
				layers = 8;
			
			//This class holds and handles a bunch of 1_wire sensors (max 50)
			Temp18B20 w1 = new Temp18B20(iniFile);
			
			//Prepare 4 IO- pins for output
			Gpio[] oPins = new Gpio[4];
			//These pins are set according to the HEX- code returned in COMMAND:$ARSET
			oPins[0] = new Gpio(5, Gpio.pintype.OUTPUT);
			oPins[1] = new Gpio(6, Gpio.pintype.OUTPUT);
			oPins[2] = new Gpio(13, Gpio.pintype.OUTPUT);
			oPins[3] = new Gpio(19, Gpio.pintype.OUTPUT);
			
			//Prepare all 12 RH IO- pins for INPUT
			Gpio[] iPins = new Gpio[14];
			//These pins are set according to the HEX- code returned in COMMAND:$ARSET
			iPins[0] = new Gpio(14, Gpio.pintype.INPUT);
			iPins[1] = new Gpio(15, Gpio.pintype.INPUT);
			iPins[2] = new Gpio(18, Gpio.pintype.INPUT);
			
			iPins[3] = new Gpio(23, Gpio.pintype.INPUT);
			iPins[4] = new Gpio(24, Gpio.pintype.INPUT);
			
			iPins[5] = new Gpio(25, Gpio.pintype.INPUT);
			iPins[6] = new Gpio(8, Gpio.pintype.INPUT);
			iPins[7] = new Gpio(7, Gpio.pintype.INPUT);
			
			iPins[8] = new Gpio(12, Gpio.pintype.INPUT);
			
			iPins[9] = new Gpio(16, Gpio.pintype.INPUT);
			iPins[10] = new Gpio(20, Gpio.pintype.INPUT);
			iPins[11] = new Gpio(21, Gpio.pintype.INPUT);
			iPins[12] = new Gpio(2, Gpio.pintype.INPUT);
			iPins[13] = new Gpio(3, Gpio.pintype.INPUT);
			
			//CHECK This pin is toggled each second, to display the software is running f.ex. by using a LED
			var  togglePin = new Gpio(26, Gpio.pintype.OUTPUT);
			
			
			//initial hex and UI- values			
			string hex = "0000"; //hex
			int ui = 0; //0-7
						
			string response;
			
			//Acceleration sensor removed for now
			//Adxl345 acc = new Adxl345();
			
			int i=0;
			while(true)
			{
				//default is to use 8 layers (0..7)
				//offset is added later, before sending to NMEA
				int layer = i%layers;
			
				//Run loop every second
				System.Threading.Thread.Sleep(1000);
				
				//toggle pin 26
				togglePin.Toggle();
				
				//Acceleration snsor removed for now
				//acc.readRollAndPitch();
				
				//XXX create new Http- call	outside of loop??			
				HttpCall hc = new HttpCall(url);
				
				hc.addGetParam("uid", uid);
				hc.addGetParam("idx", i.ToString()); // message index
				
				string temps = "";
				
				//steal the second layer (layer 1) for acceleration data
				/*
				if (i%8 == 1)
				{		
					acc.readRollAndPitch();
					temps =  ","+acc.getRoll()+","+acc.getPitch()+",0,0,0,0";
				}
				else
				*/
				{
					for(int j=0; j < 6; j++)
					{
						temps += "," + w1.getSensorTemp(layer * 6 + j, 2);
					}
				}
				
				//add offset to layer before sending
				layer += layerOffset;
				 
				//create arana 0..7
				string arana = "$ARANA," + layer + temps + "*ff";
				
				//Console.WriteLine(arana);
				
				var ardioData = "";
				
				for(int j = 0; j < 14; j++)
				{
					ardioData += iPins[j].isOn() ? "1," : "0,";
				}
				
				//ardio is always in layer 0
				string ardio = "$ARDIO," + "0" + "," + ardioData.TrimEnd(",".ToCharArray()) + "*ff";
				
				string arsta = "$ARSTA,"+ui+","+hex+"*ff";
				//urlencode pipes as %7C
				hc.addGetParam("nmea", ardio + "%7C" + arana + "%7C" + arsta);
				
				Console.WriteLine("Send:");
				Console.WriteLine(hc.getRequestString());
				
				response = hc.getHtml();
				
				if (response.Contains("COMMAND:$ARSET"))
				    {
				    	string arset = response.Substring(response.IndexOf("COMMAND:$ARSET"));
				    	string[] parts = arset.Split(",*".ToCharArray());
				    	hex = parts[1];
				    	
				    	HexCode hCode = new HexCode(hex);
				    	
				    	//check pins
				    	for(int j=0; j < 4; j++)
				    	{
				    		if (oPins[j].isOn() != hCode.getBits()[j])
				    			oPins[j].Toggle();
				    	}
				    	
				    	hex = hCode.getHex();
				    }
				Console.WriteLine("Receive:");
				Console.WriteLine(response);
				Console.WriteLine("GCIO-------------------------------------------------------");
				//collect garbage
				GC.Collect();			
				i++;
			}
		}
	}
}