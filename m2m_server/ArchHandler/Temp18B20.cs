/*
 * Created by SharpDevelop.
 * User: stefan
 * Date: 16.11.2015
 * Time: 21:41
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.IO;

namespace ArchHandler
{
	/// <summary>
	/// Description of Class1.
	/// </summary>
	public class Temp18B20
	{
		string sysPath = "/sys/bus/w1/devices/";
		Sensor[] sensors = new Sensor[50];
		int sensorCount = 0;
		
		class Sensor
		{
			private string path;
			private string name;
			
			public Sensor(string path, string name)
			{
				this.path = path;
				this.name = name;
			}
			
			public string getPath()
			{
				return this.path;
			}
			
			public string getName()
			{
				return this.name;
			}
			
			public string[] getData()
			{
				return TextFiles.readLines(this.path +"/w1_slave");
			}
			
			public string getTempString()
			{
				string[] lines = this.getData();
				
				if (lines[0].EndsWith("YES"))
			    {
					return lines[1].Substring(lines[1].LastIndexOf('=')+1);
			    }
				    return null;
			}
			
			public double getTemp(int digits)
			{
				
				try 
				{
					string val = this.getTempString();
					return Math.Round(Convert.ToDouble(val) / 1000.0, digits);
					
				}
				catch(Exception)
				{
					return -99;
				}
			}
		}
		
		public Temp18B20(IniGroupFileRW iniFile)
		{
			readDirectory(iniFile);
		}
		
		//Read directory at startup and assign sensors
		private void readDirectory(IniGroupFileRW iniFile)
		{
			//TEMP for possible new sensors
			var newSensors = new Sensor[50];
			
			foreach(string sensor in Directory.GetDirectories(this.sysPath))
			{
				string sensorName = sensor.Substring(this.sysPath.Length);
				
				int newIndex = 0;
				//if sensor is already in iniFile, re-use existing number
				if (iniFile.keyExists(sensorName, "1wire"))
			    {
				    	int sensorNumber = Convert.ToInt32(iniFile.getValue(sensorName, "1wire"));
			 		    this.sensors[sensorNumber] = new Sensor(sensor, sensorName);
			 		    this.sensorCount++;
			    }
				    
				//sensor directory starts with 28
				else if (sensorName.StartsWith("28"))
				{	
					//add new sensors to TEMP local array for now
					newSensors[newIndex++] = new Sensor(sensor, sensorName);
					sensorCount++;
				}
			}
			
			//iterate all new sensors, and put them in firs empty spaces
			foreach (Sensor ns in newSensors)
			{
				if (ns == null)
					continue;
				
				for (int i = 0; i < 50; i++)
				{
					if (this.sensors[i] == null)
					{
						this.sensors[i] = ns;
						iniFile.addSetting(ns.getName(), i.ToString(), "1wire");
						break;
					}
				}
			}
			
		}
		
		public void printSensors()
		{
			for (int i=0; i < this.sensorCount; i++)
			{
				Console.WriteLine(this.sensors[i].getName() + "-" + this.sensors[i].getTemp(2));
			}
		}
		
		public double getSensorTemp(int idx, int digits)
		{
			if (this.sensorCount >= idx +1)
				return this.sensors[idx].getTemp(digits);
		
			return -99;
		}
		
		public int getSensorCount()
		{
			return this.sensorCount;
		}
	}
}
