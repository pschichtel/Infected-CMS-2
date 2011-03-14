<?php
    /**
     *
     */
    class Smtp
    {
        /**
         * Email successfully sent
         */
        const OK                =  0;
        
        /**
         * Remote connection failed
         */
        const CONNECTION_FAILURE = -1;
        
        /**
         * The responded with the wrong status code
         */
        const INVALID_RESPONSE = -2;
        
        /**
         * Failed to send a command
         */
        const SEND_FAILURE = -3;
        
        /**
         * An unknown error occurred
         */
        const UNKNOWN           = 1;
        
        protected $config;
        protected $connection;
        protected $connected;
        protected $error;

        /**
         * Creates a object of the Smtp-class if a valid configuration was given.
         * Throws a NetworkException an failure.
         *
         * @access public
         * @param string[] $config the configuration
         */
        public function __construct(array $config)
        {
            if (!$this->validateConfig($config))
            {
                throw new NetworkException('[Smtp::__construct] The given configuration array does not contain the needed keys!');
            }
            $this->config = $config;
            $this->connection = null;
            $this->connected = false;
        }
        
        /**
         * Destructs the objects
         * 
         * @access public
         */
        public function __destruct()
        {
            $this->disconnect();
        }
        
        /**
         * Gets the last error
         *
         * @access public
         * @return string the last occurred or a empty string
         */
        public function error()
        {
            $tmp = $this->error;
            $this->error = '';
            return $tmp;
        }

        /**
         * Validates the given array
         *
         * @access protected
         * @param array $config the configuration-array to validate
         * @return bool whether the configuration-array is valid or not
         */
        protected function validateConfig(array& $config)
        {
            return isset($config['host'], $config['port'], $config['sender']);
        }
        
        /**
         * Opens a socket connection to the Smtp server
         * 
         * @access protected
         */
        protected function connect()
        {
            if ($this->connected)
            {
                throw new NetworkException('', Smtp::CONNECTION_FAILURE);
            }
            $this->connection = fsockopen($this->config['host'], $this->config['port'], $errno, $errstr, 3);
            if ($this->connection === false)
            {
                throw new NetworkException('Failed to connect to the remote server!', Smtp::CONNECTION_FAILURE);
            }
            $this->connected = true;
        }
        
        /**
         * Closes the connection the the Smtp server if stil open
         * 
         * @access protected
         */
        protected function disconnect()
        {
            if ($this->connected && is_resource($this->connection))
            {
                @fclose($this->connection);
                $this->connected = false;
            }
        }


        /**
         * sends an email with the given data
         *
         * @access public
         * @param string $to address to send to
         * @param string $from the sender address
         * @param string $subject the subject of the email
         * @param string $text the email content
         * @param string[] $headers additional headers
         * @return int return code, 0 un success
         */
        public function mail($to, $from, $subject, $text, array $headers = array())
        {
            try
            {
                $this->connect();
                
                $servername = 'localhost';
                if (isset($_SERVER['SERVER_NAME']) && trim($_SERVER['SERVER_NAME']) !== '')
                {
                    $servername = $_SERVER['SERVER_NAME'];
                }
                
                $this->parseResponse($this->receive(), 220);
                $this->send('HELO ' . $servername, 250);
                if (isset($this->config['user'], $this->config['pass']))
                {
                    if (!empty($this->config['user']) && !empty($this->config['pass']))
                    {
                        $this->send('AUTH LOGIN', 334);
                        $this->send(base64_encode($this->config['user']), 334);
                        $this->send(base64_encode($this->config['pass']), 235);
                    }
                }
                $this->send('MAIL FROM: ' . $this->config['sender']/* . ''*/, 250);
                $this->send('RCPT TO: ' . str_replace(' ', '\\ ', $to), 250);
                $this->send('DATA', 354);

                $this->send('Subject: ' . $subject);
                $this->send('To: ' . $to);
                $this->send('From: ' . $from);
                $this->send(implode("\r\n", $headers));
                
                $this->send("\r\n");
                $this->send($text);
                $this->send('.', 250);
                
                $this->send('QUIT', 0);
                $this->disconnect();
                return Smtp::OK;
            }
            catch (NetworkException $e)
            {
                $this->disconnect();
                $this->error = $e->getMessage();
                return $e->getCode();
            }
            catch (Exception $e)
            {
                $this->disconnect();
                $this->error = $e->getMessage();
                return Smtp::UNKNOWN;
            }
            
            /*
             * The finally-structure would be useful here :/
             */
        }

        /**
         * Sends a command and checks whether the server responds with the correct status code
         *
         * @access protected
         * @param string $command the command to send
         * @param int $sucesscode the code which the server returns on success
         */
        protected function send($command, $sucesscode = 0)
        {
            if (@fwrite($this->connection, $command . "\r\n") === false)
            {
                throw new NetworkException('Failed to send the command "' . $command . '" !', Smtp::SEND_FAILURE);
            }
            if ($sucesscode !== 0)
            {
                $response = $this->receive();
                $this->parseResponse($response, $sucesscode);
            }
        }

        /**
         * receives data from the SMTP server
         *
         * @access protected
         * @return string the received data as a trimed string
         */
        protected function receive()
        {
            $response = '';
            while(($tmp = trim(@fgets($this->connection, 513))))
            {
                $response .= $tmp;
                if (mb_substr($tmp, 3, 1) == ' ')
                {
                    break;
                }
            }
            
            return trim($response);
        }

        /**
         * Parses the response and checks if the response contains the given status code
         *
         * @access protected
         * @param string $response the response data
         * @param code $code the needed code
         */
        protected function parseResponse($response, $code)
        {
            $responsecode = mb_substr($response, 0, 3);
            if (trim($responsecode) != trim($code))
            {
                throw new NetworkException('The server responded(Response: "' . $response . '") with the wrong status code!', Smtp::INVALID_RESPONSE);
            }
        }

        /**
         * A wrapper of Smtp::mail()
         *
         * @access public
         * @param string $to the email target
         * @param string $from the sender
         * @param string $subject the email subject
         * @param string $text the content
         * @param string $charset the charset (default: UTF-8)
         * @return int the return code from Smtp::mail() which indicates success (0) or failure (not 0)
         */
        public function sendmail($to, $from, $subject, $text, $charset = 'UTF-8')
        {
            if (!Text::is_email($to) || !Text::is_email($from))
            {
                throw new Exception('[Smtp::sendmail] The receiver and sender addresses must both be valid email addresses!');
            }
            list($name, $host) = explode('@', $to);
            $name = ucwords(str_replace(array('_', '.', '-'), ' ', $name));
            $to = $name . '<' . $to . '>';

            list($name, $host) = explode('@', $from);
            $name = ucwords(str_replace(array('_', '.', '-'), ' ', $name));
            $from = $name . '<' . $from . '>';

            return $this->mail($to, $from, $subject, $text, array('Content-type:text/plain;charset=' . $charset));
        }
    }
?>