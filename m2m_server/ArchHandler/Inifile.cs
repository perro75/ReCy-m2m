using System;

namespace ArchHandler
{
    /// <summary>
    /// This class reads and writes settings using a textfile.
    /// Settings are grouped into different groups and written into a clear-text format.
    /// </summary>
    public class IniGroupFileRW
    {
        private string filename;
        private char splitterChar = '=';
        private string generalGroup = "General";
        private System.Collections.ArrayList settings = new System.Collections.ArrayList();

        /// <summary>
        /// Constructor
        /// </summary>
        /// <param name="filename">Name of the file to open or create</param>
        public IniGroupFileRW(string filename)
        {
            this.filename = filename;

            if (TextFiles.fileExists(this.filename))
            {
                //read the file into memory
                this.readSettings();
            }
            else
            {
                string[] firstLine = new string[1];
                firstLine[0] = "[" + generalGroup + "]";

                TextFiles.writeNewFile(this.filename, firstLine);
            }
        }

        /// <summary>
        /// Count total amount of settings in this file
        /// </summary>
        /// <returns></returns>
        public int countSettings()
        {
            return this.settings.Count;
        }

        /// <summary>
        /// Get the value of specified key from general group
        /// </summary>
        /// <param name="key"></param>
        /// <returns>The string value or null if key do not exist</returns>
        public string getValue(string key)
        {
            return getValue(key, this.generalGroup);
        }


        /// <summary>
        /// Get the value of specified key from specified group
        /// </summary>
        /// <param name="key"></param>
        /// <param name="group"></param>
        /// <returns>The string value or null if key do not exist</returns>
        public string getValue(string key, string group)
        {
            foreach (IniSetting ini in settings)
            {
                if (ini.getKey().Equals(key) && ini.getGroup().Equals("[" + group + "]"))
                {
                    return ini.getValue();
                }
            }
            return null;
        }

        /// <summary>
        /// Remove setting from general group
        /// </summary>
        /// <param name="key"></param>
        public void removeSetting(string key)
        {
            removeSetting(key, this.generalGroup);
        }

        /// <summary>
        /// Remove setting from specified group
        /// </summary>
        /// <param name="key"></param>
        /// <param name="group"></param>
        public void removeSetting(string key, string group)
        {
            foreach (IniSetting ini in this.settings)
            {
                if (ini.getKey().Equals(key) && ini.getGroup().Equals("[" + group + "]"))
                {
                    settings.Remove(ini);
                    saveFile();
                    break;
                }
            }
        }

        /// <summary>
        /// Add setting or update value for key in specified group.
        /// File is automatically saved after each update.
        /// </summary>
        /// <param name="key"></param>
        /// <param name="value"></param>
        /// <param name="group"></param>
        /// <returns></returns>
        public void addSetting(string key, string value, string group)
        {
            //check if key already present
            IniSetting ini;
            if ((ini = findSetting(key, group)) != null)
            {
                ini.setValue(value);
            }
            else
            {
                this.settings.Add(new IniSetting(key, value, "[" + group + "]"));
            }

            saveFile();
        }

        /// <summary>
        /// Add setting or update value for key in general group.
        /// File is automatically saved after each update.
        /// </summary>
        /// <param name="key"></param>
        /// <param name="value"></param>
        /// <returns></returns>
        public void addSetting(string key, string value)
        {
            addSetting(key, value, this.generalGroup);
        }

        /// <summary>
        /// Check if a key exists in specified group
        /// </summary>
        /// <param name="key"></param>
        /// <param name="group"></param>
        /// <returns></returns>
        public bool keyExists(string key, string group)
        {
            foreach (IniSetting ini in this.settings)
            {
                if (ini.getGroup().Equals("[" + group + "]") && ini.getKey().Equals(key))
                {
                    return true;
                }
            }
            return false;
        }

        /// <summary>
        /// Check if a key exists in general group
        /// </summary>
        /// <param name="key"></param>
        /// <returns></returns>
        public bool keyExists(string key)
        {
            return keyExists(key, this.generalGroup);
        }

        /// <summary>
        /// Find a setting by key and group.
        /// </summary>
        /// <param name="key"></param>
        /// <param name="group"></param>
        /// <returns></returns>
        private IniSetting findSetting(string key, string group)
        {
            foreach (IniSetting ini in this.settings)
            {
                if (ini.getGroup().Equals("[" + group + "]") && ini.getKey().Equals(key))
                {
                    return ini;
                }
            }
            return null;
        }

        /// <summary>
        /// Save the file. Function called when updated or added settings.
        /// </summary>
        private void saveFile()
        {
            settings.Sort();
            string[] lines = new string[settings.Count];
            string group = null;
            int i = 0;

            foreach (IniSetting ini in settings)
            {
                //New group
                if (ini.getGroup().Equals(group) == false)
                {
                    group = ini.getGroup();
                    lines[i] = group + Environment.NewLine;
                }

                lines[i++] += ini.getKey() + splitterChar + ini.getValue();
            }
            TextFiles.writeNewFile(this.filename, lines);
        }

        /// <summary>
        /// Read settings into array.
        /// Function called by constructor.
        /// </summary>
        private void readSettings()
        {
            string[] lines = TextFiles.readLines(this.filename);
            string group = this.generalGroup;

            foreach (string line in lines)
            {
                //New group
                if (line.StartsWith("[") && line.EndsWith("]"))
                {
                    group = line;
                    continue;
                }
                string[] parts = line.Split(this.splitterChar);
                try
                {
                    settings.Add(new IniSetting(parts[0], parts[1], group));
                }
                catch (IndexOutOfRangeException)
                {
                    //fault
                }
            }
        }
        /// <summary>
        /// Delete the settingsfile. The file will be recreated 
        /// if an insert or update request is done after this function.
        /// </summary>
        public void deleteFile()
        {
            TextFiles.deleteFile(this.filename);
        }

        /// <summary>
        /// Class contains one Setting
        /// </summary>
        class IniSetting : IComparable
        {
            private string key;
            private string value;
            private string group;

            /// <summary>
            /// Class is comparable using a string consisiting of group+key
            /// </summary>
            /// <param name="other"></param>
            /// <returns></returns>
            public int CompareTo(Object other)
            {
                IniSetting set = (IniSetting)other;
                return this.getSort().CompareTo(set.getSort());
            }

            /// <summary>
            /// At least key and value must be given
            /// </summary>
            /// <param name="key"></param>
            /// <param name="value"></param>
            /// <param name="group"></param>
            public IniSetting(string key, string value, string group)
            {
                this.key = key;
                this.value = value;
                this.group = group;
            }

            public string getKey() { return this.key; }
            public string getValue() { return this.value; }
            public string getGroup() { return this.group; }

            /// <summary>
            /// Provides the search-string for sorting
            /// </summary>
            /// <returns></returns>
            private string getSort() { return this.group + this.key; }

            /// <summary>
            /// Sets value in case changing value for existing key
            /// </summary>
            /// <param name="value"></param>
            public void setValue(string value) { this.value = value; }
        }

    }
}

