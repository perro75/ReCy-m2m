using System;
using System.IO;

namespace ArchHandler
{
    /// <summary>
    /// This class contains static functions for reading and writing arrays of strings to simple textfiles
    /// </summary>
    public static class TextFiles
    {
        /// <summary>
        /// Write to a new or replace existing file
        /// </summary>
        /// <param name="filename"></param>
        /// <param name="lines"></param>
        public static void writeNewFile(string filename, string[] lines)
        {
            writeFile(filename, lines, false);
        }

        /// <summary>
        /// Write to a new or replace existing file
        /// </summary>
        /// <param name="filename"></param>
        /// <param name="lines"></param>
        public static void writeNewFile(string filename, string line)
        {
            string[] lines = { line };
            writeFile(filename, lines, false);
        }



        /// <summary>
        /// Write to a new or append lines to a textfile
        /// </summary>
        /// <param name="filename"></param>
        /// <param name="lines"></param>
        public static void appendToFile(string filename, string[] lines)
        {
            writeFile(filename, lines, true);
        }

        /// <summary>
        /// Write to a new or append lines to a textfile
        /// </summary>
        /// <param name="filename"></param>
        /// <param name="lines"></param>
        public static void appendToFile(string filename, string line)
        {
            string[] lines = { line };
            writeFile(filename, lines, true);
        }

        /// <summary>
        /// Write lines to textfile
        /// </summary>
        /// <param name="filename"></param>
        /// <param name="lines"></param>
        /// <param name="append"></param>
        private static void writeFile(string filename, string[] lines, bool append)
        {
            TextWriter wr = new StreamWriter(filename, append);

            //Read lines from placeholder
            for (int i = 0; i < lines.Length; i++)
            {
                wr.WriteLine(lines[i]);
            }
            wr.Close();
        }

        /// <summary>
        /// Count the lines in a textfile
        /// </summary>
        /// <param name="filename"></param>
        /// <returns></returns>
        public static int countLines(string filename)
        {
            TextReader rf = new StreamReader(filename);
            int i = 0;
            //count lines
            while (rf.ReadLine() != null) { i++; }

            rf.Close();
            return i;
        }

        /// <summary>
        /// Read textfiles lines into an array of strings
        /// </summary>
        /// <returns></returns>
        public static string[] readLines(string filename)
        {
            int lineCount = countLines(filename);
            TextReader rf = new StreamReader(filename);
            string[] lines = new string[lineCount];

            for (int i = 0; i < lines.Length; i++)
            {
                lines[i] = rf.ReadLine();
            }
            rf.Close();
            return lines;
        }

        /// <summary>
        /// Check if a file exists
        /// </summary>
        /// <returns></returns>
        public static bool fileExists(string filename)
        {
            return File.Exists(filename);
        }

        /// <summary>
        /// Delete file
        /// </summary>
        /// <param name="filename"></param>
        public static void deleteFile(string filename)
        {
            File.Delete(filename);
        }

        /// <summary>
        /// Copy a file. If the destination already exists, it will be overwritten.
        /// </summary>
        /// <param name="filename"></param>
        /// <param name="newname"></param>
        public static void copyFile(string filename, string newname)
        {
            File.Copy(filename, newname, true);
        }

        /// <summary>
        /// Move a file. If the destination already exists, it will be overwritten.
        /// </summary>
        /// <param name="filename"></param>
        /// <param name="newname"></param>
        public static void moveFile(string filename, string newname)
        {
            copyFile(filename, newname);
            deleteFile(filename);
        }

    }
}
