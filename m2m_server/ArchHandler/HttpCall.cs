using System;
using System.Net;
using System.IO;
using System.Text;

namespace ArchHandler
{
    /// <summary>
    /// Class sends a GET-query to a given URL and returns the received HTML.
    /// Main purpose is to communicate with a database etc. through a php-page.
    /// Request can include basic authentication.
    /// </summary>
    public class HttpCall
    {
        string url;
        string user;
        string pswd;

        HttpWebRequest req;

        //GET parameters
        string[] getKey = new string[10];
        string[] getValue = new string[10];
        int getIdx = 0;

        /// <summary>
        /// Create a new instance by specifying the URL
        /// </summary>
        /// <param name="url"></param>
        public HttpCall(string url)
        {
            this.url = url;
        }

        /// <summary>
        /// Specify a new instance using BASIC AUTHENTICATION
        /// </summary>
        /// <param name="url"></param>
        /// <param name="user"></param>
        /// <param name="pswd"></param>
        public HttpCall(string url, string user, string pswd)
        {
            this.url = url;
            this.user = user;
            this.pswd = pswd;
        }

        /// <summary>
        /// Add a GET parameter to the request.
        /// </summary>
        /// <param name="GET"></param>
        /// <returns>number of parameters given</returns>
        public int addGetParam(string key, string value)
        {
            try
            {
                this.getKey[getIdx] = key;
                this.getValue[getIdx] = value;
            }
            catch (IndexOutOfRangeException)
            {
                return -1;
            }

            return getIdx++;
        }
		
        public string getRequestString()
        {
        	string get = "?";
            for (int i = 0; i < getIdx; i++)
            {
                get += getKey[i] + "=" + getValue[i] + "&";
            }

            return url + get;
            
        }
        
        
        /// <summary>
        /// Get the html of the url as a string of text.
        /// </summary>
        /// <param name="url"></param>
        /// <returns></returns>
        public string getHtml()
        {

            
        	req = (HttpWebRequest)WebRequest.Create(this.getRequestString());

            //System.Diagnostics.Debug.Print(url + get);

            if (user != null && pswd != null)
            {
                req.Headers.Add("Authorization", "Basic " + Convert.ToBase64String(Encoding.ASCII.GetBytes(user + ":" + pswd)));
            }

            string html = string.Empty;

            try
            {
                using (HttpWebResponse resp = (HttpWebResponse)req.GetResponse())
                {
                    bool isSuccess = (int)resp.StatusCode < 299 && (int)resp.StatusCode >= 200;
                    if (isSuccess)
                    {
                        using (StreamReader reader = new StreamReader(resp.GetResponseStream()))
                        {
                            html = reader.ReadToEnd();
                        }
                    }
                }
            }
            catch (Exception)
            {
                return "#ERROR";
            }

            return html;
        }
    }
}

