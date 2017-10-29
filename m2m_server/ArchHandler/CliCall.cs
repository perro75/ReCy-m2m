/*
 * Created by SharpDevelop.
 * User: stefan
 * Date: 17.11.2015
 * Time: 21:34
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.Diagnostics;

namespace ArchHandler
{
	/// <summary>
	/// Description of CliCall.
	/// </summary>
	public class CliCall
	{
		private Process process = new Process();
	    private string output = null, error = null;
	    
		public CliCall(string command, string arguments)
		{
	    
		this.process.StartInfo.FileName = command;
	    this.process.StartInfo.Arguments = arguments; 
	    
	    this.process.StartInfo.UseShellExecute = false;
	    this.process.StartInfo.RedirectStandardOutput = true;
	    this.process.StartInfo.RedirectStandardError = true;
	    }

		public string execute()
		{
		
		process.Start();
	    
 		this.output = process.StandardOutput.ReadToEnd();
	    this.error = process.StandardError.ReadToEnd();
	    process.WaitForExit();
		
	    return this.output;
		}
		
		public string getError()
		{
			return this.error;
		}
		                       
	    
	}
}
