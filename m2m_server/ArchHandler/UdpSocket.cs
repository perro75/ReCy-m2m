using System;
using System.Net;
using System.Net.Sockets;
using System.Threading;

namespace ArchHandler
{
    /// <summary>
    /// This is a simple class for sending and receiving UDP messages. 
    /// UdpSocket has a send() method for sending strings to the receiver.
    /// A separate Thread is used to scan and receive messages.
    /// An Event is fired when a message is received.
    /// The message string is passed in a UdpEventArgs-object. 
    /// A timeout (default 5000ms) is set for the scanning Thread.
    /// If the timeout limit is exceeded, an Event is fired.
    /// This event can be used to check activity / inactivity etc. of the scanner.
    /// </summary>
    public class UdpSocket
    {
        private Socket s;
        private IPEndPoint ipep;

        private Byte[] receiveBuffer;
        private int buffersize;

        private Thread scan;
        private int rcvTimeout = 5000;

        /// <summary>
        /// Get or Set the receive-timeout in milliseconds. This is also the maximum time a receiver may wait
        /// before aborting due to a external request to abort. Default is 5000ms. Timeout can be changed during
        /// scanning, and is updated after the next timeout or received message.
        /// </summary>
        public int ReceiveTimeout
        {
            get { return rcvTimeout; }
            set { rcvTimeout = value; }
        }

        /// <summary>
        /// Constructor. The object must be connected before use.
        /// </summary>
        /// <param name="mcastGroup">The IP-address to use</param>
        /// <param name="remotePort">The port to use</param>
        public UdpSocket(string ipAddress, int remotePort)
        {
            this.construct(ipAddress, remotePort, 0); //srcPort default 0
        }

        /// <summary>
        /// Constructor. The object must be connected before use.
        /// </summary>
        /// <param name="mcastGroup">The IP-address to use</param>
        /// <param name="remoteport">The port to use</param>
        /// <param name="localPort">The specific Source port to bind to</param>
        public UdpSocket(string ipAddress, int remotePort, int localPort)
        {
            this.construct(ipAddress, remotePort, localPort);
        }

        private void construct(string ipAddress, int port, int srcPort)
        {
            IPAddress ip = IPAddress.Parse(ipAddress);
            s = new Socket(AddressFamily.InterNetwork, SocketType.Dgram, ProtocolType.Udp);
            s.Bind(new IPEndPoint(IPAddress.Any, srcPort)); //bind to sourceport
            ipep = new IPEndPoint(IPAddress.Parse(ipAddress), port);
        }

        /// <summary>
        /// Connect the socket if disconnected. Use a specific TTL-value instead of the default 32.
        /// The TTL-value affects a multicast connection. If socket is already connected, only TTL is updated.
        /// </summary>
        /// <param name="ttl"></param>
        public void connect(int multicastTimeToLive)
        {
            //set ttl
            s.SetSocketOption(SocketOptionLevel.IP, SocketOptionName.MulticastTimeToLive, multicastTimeToLive);
            this.connect();
        }

        /// <summary>
        /// Connect the socket if disconnected. If socket is already connected, nothing happens.
        /// </summary>
        public void connect()
        {
            if (s.Connected == false) s.Connect(this.ipep);
        }

        /// <summary>
        /// Close this socket. All resources are freed and 
        /// the socket cannot be connected again.
        /// </summary>
        public void close()
        {
            if (s.Connected)
            {
                s.Shutdown(SocketShutdown.Both);
                s.Close();
            }
        }

        /// <summary>
        /// Send a sentence by UDP.
        /// </summary>
        /// <param name="sentence"></param>
        public void sendUdp(string sentence)
        {
            System.Text.UTF8Encoding encoding = new System.Text.UTF8Encoding();
            byte[] b = encoding.GetBytes(sentence);
            s.Send(b);
        }

        //*********************RECEIVE***********************

        /// <summary>
        /// Start receiving data. A MessageReceived-event is raised when a message is received.
        /// The socket shall NOT be connected when used for receiving!!
        /// </summary>
        /// <param name="bufferSize">The size of the buffer in bytes.</param>
        public void startReceive(int buffersize)
        {
            this.buffersize = buffersize;
            this.receiveBuffer = new byte[this.buffersize];
            this.startReceiving();
        }

        /// <summary>
        /// Start receiving data. A MessageReceived-event is raised when a message is received.
        /// If buffer is not specified, default size is 256.
        /// </summary>
        public void startReceive()
        {
            this.buffersize = 256;
            this.receiveBuffer = new byte[this.buffersize];
            this.startReceiving();
        }

        private void startReceiving()
        {
            //only create thread if not existing
            if (this.scan == null)
            {
                this.scan = new Thread(this.startScan);
                this.scan.IsBackground = true;
            }

            scan.Start();
        }

        /// <summary>
        /// Suspend the receiving.
        /// </summary>
        public void stopReceive()
        {
            scan.Abort();
        }

        /// <summary>
        /// The thread that scans the port.
        /// </summary>
        private void startScan()
        {
            int bytes = 0;

            while (true)
            {
                s.ReceiveTimeout = this.rcvTimeout; // set timeout to ensure aborting
                try
                {
                    //bytes gets size of string
                    bytes = s.Receive(this.receiveBuffer, this.buffersize, SocketFlags.None); //if buffer overrun, exception is thrown
                }
                catch (SocketException ex)
                {
                    if (ex.ErrorCode == (int)SocketError.MessageSize)
                    {
                        if (BufferExceeded != null)
                        {
                            BufferExceeded.Invoke(this, new UdpSocketEventArgs("Buffer of " + this.buffersize + " bytes exceeded on port: " + this.ipep.Port));
                        }
                    }

                    if (ex.ErrorCode == (int)SocketError.TimedOut)
                    {

                        if (ReceiverTimeout != null)
                        {
                            //timeout
                            ReceiverTimeout.Invoke(this, new UdpSocketEventArgs("No message received in " + this.ReceiveTimeout + "ms on port: " + this.ipep.Port, ex.Message));
                            bytes = -1;
                        }
                    }
                }

                finally
                {
                    //bytes -1 marks a timeout
                    //bytes 0 marks buffer overrun
                    // any other amount is the amount of bytes received
                    if (bytes >= 0)
                    {
                        //string message = System.Text.Encoding.UTF8.GetString(receiveBuffer);
                        string message = System.Text.ASCIIEncoding.UTF8.GetString(receiveBuffer);

                        //causes Exception if this is not listened for!
                        MessageReceived.Invoke(this, new UdpSocketEventArgs(message.Substring(0, bytes == 0 ? this.buffersize : bytes)));
                    }
                }
            }
        }

        public delegate void MessageReceivedHandler(object sender, UdpSocketEventArgs e);
        /// <summary>
        /// The event is raised when the socket receives a message.
        /// </summary>
        public event MessageReceivedHandler MessageReceived;

        public delegate void ReceiverTimeoutHandler(object sender, UdpSocketEventArgs e);
        /// <summary>
        /// The event is raised when the socket ReceiveTimeout property is exceeded with no incoming messages.
        /// To reset any status of inactivity, the MessageReceived event may be used.
        /// </summary>
        public event ReceiverTimeoutHandler ReceiverTimeout;

        public delegate void BufferExceededHandler(object sender, UdpSocketEventArgs e);
        /// <summary>
        /// The event is raised when the socket receivebuffer is exceeded.
        /// </summary>
        public event BufferExceededHandler BufferExceeded;
    }
    /// <summary>
    /// The class contains a passed message and optional system-message.
    /// </summary>
    public class UdpSocketEventArgs : EventArgs
    {
        private string message;
        private string systemMessage;

        /// <summary>
        /// Create a new instance of a UdpEventArgs class.
        /// </summary>
        /// <param name="message">A received or generated message as string. The contents of message
        /// depends on the Event sending it.</param>
        public UdpSocketEventArgs(string message)
            : base()
        {
            this.message = message;
        }

        /// <summary>
        /// Create a new instance of a UdpEventArgs class.
        /// </summary>
        /// <param name="message">A received or generated message as string. The contents of message
        /// depends on the Event sending it.</param>
        /// <param name="systemMessage">An additional system-message to add to the Event-argument</param>
        public UdpSocketEventArgs(string message, string systemMessage)
            : base()
        {
            this.message = message;
            this.systemMessage = systemMessage;
        }
        /// <summary>
        /// Get the message received by the event.
        /// </summary>
        /// <returns></returns>
        public string getMessage()
        {
            return this.message;
        }

        /// <summary>
        /// Get the system-message received by the event.
        /// This is usually a message generated by an underlying Exception or a handler.
        /// The content of this message depends on the Event sending it.
        /// </summary>
        /// <returns></returns>
        public string getSystemMessage()
        {
            return this.systemMessage;
        }
    }
}


