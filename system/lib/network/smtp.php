<?php
    /**
     *
     */
    class SMTP
    {
        /**
         * Email successfully sent
         */
        const OK                =  0;
        /**
         * Remote connection failed
         */
        const CONNECTION_FAILED = -1;
        /**
         * failed at HELO
         */
        const HELO_FAILED       = -2;
        /**
         * incorrect username was given and login failed
         */
        const WRONG_USER        = -3;
        /**
         * incorrect password was given and login failed
         */
        const WRONG_PASS        = -4;
        /**
         * failed at RCPT command, this has to be a valid email address as well
         */
        const WRONG_FROM        = -5;
        /**
         * failed at DATA command, maybe there were invalid or broken characters given (charset mismatches?)
         */
        const WRONG_RCPT        = -6;
        /**
         * completation of the data block failed
         */
        const DATA_FAILED       = -7;
        /**
         * completation of the data block failed
         */
        const COMPLETE_FAILED   = -8;
        /**
         * an unknown error occurred
         */
        const UNKNOWN           = -9;

        /**
         * Validates the given array
         *
         * @param array $config the configuration-array to validate
         * @return bool whether the configuration-array is valid or not
         */
        private static function validateConfig(&$config)
        {
            return isset($config['host'], $config['port'], $config['user'], $config['pass'], $config['sender']);
        }

        private function __construct($config)
        {
            $this->config = $config;
        }

        /**
         * Generates a new SMTP-object
         *
         * @param mixed $config the configuration
         * @param bool $ini_file if true $config is used as the filepath to an ini-file with the configuration
         * @return SMTP
         */
        public static function factory($config, $ini_file = false)
        {
            if ($ini_file)
            {
                $config = parse_ini_file($config);
            }
            if (!is_array($config))
            {
                return false;
            }
            elseif (!self::validateConfig($config))
            {
                return false;
            }
            else
            {
                return new SMTP($config);
            }
        }


        /**
         * sends an email with the given data
         *
         * @static
         * @access public
         * @param string $to address to send to
         * @param string $from the sender address
         * @param string $subject the subject of the email
         * @param string $text the email content
         * @param array $headers additional headers
         * @return int return code
         */
        public function mail($to, $from, $subject, $text, $headers = null)
        {
            $smtp = fsockopen($this->config['host'], $this->config['port'], $errno, $errstr, 3);
            if ($smtp === false)
            {
                return self::CONNECTION_FAILED;
            }
            if (!$this->parse($this->receive($smtp), 220))
            {
                return self::UNKNOWN;
            }
            if (!$this->send($smtp, 'HELO ' . $_SERVER['SERVER_NAME'], 250))
            {
                return self::HELO_FAILED;
            }
            if ($this->config['user'] && $this->config['pass'])
            {
                if (!$this->send($smtp, 'AUTH LOGIN', 334))
                {
                    return self::UNKNOWN;
                }
                if (!$this->send($smtp, base64_encode($this->config['user']), 334))
                {
                    return self::WRONG_USER;
                }
                if (!$this->send($smtp, base64_encode($this->config['pass']), 235))
                {
                    return self::WRONG_PASS;
                }
            }
            if (!$this->send($smtp, 'MAIL FROM: ' . $this->config['sender'] . '', 250))
            {
                return self::WRONG_FROM;
            }
            if (!$this->send($smtp, 'RCPT TO: ' . $to . '', 250))
            {
                return self::WRONG_RCPT;
            }
            if (!$this->send($smtp, 'DATA', 354))
            {
                return self::DATA_FAILED;
            }

            $this->send($smtp, 'Subject: ' . $subject, 0);
            $this->send($smtp, 'To: ' . $to, 0);
            $this->send($smtp, 'From: ' . $from, 0);
            if (is_array($headers))
            {
                $this->send($smtp, implode("\r\n", $headers), 0);
            }
            $this->send($smtp, "\r\n", 0);
            $this->send($smtp, $text, 0);

            if (!$this->send($smtp, '.', 250))
            {
                return self::COMPLETE_FAILED;
            }
            $this->send($smtp, 'QUIT', 0);
            fclose($smtp);
            return self::OK;
        }

        /**
         * sends $command and checks whether $successcode is equal to the returned code
         *
         * @static
         * @access protected
         * @param resource $handle the socket handle
         * @param string $command the command to send
         * @param int $sucesscode the code which the server returns on success
         * @return bool true on success, false on failure
         */
        protected function send($handle, $command, $sucesscode)
        {
            fputs($handle, $command . "\r\n");
            if ($sucesscode)
            {
                $response = $this->receive($handle);
                if ($this->parse($response, $sucesscode))
                {
                    return true;
                }
                else
                {
                    fclose($handle);
                    return false;
                }
            }
        }

        /**
         * receives data from the SMTP server
         *
         * @static
         * @access protected
         * @param resource $handle the socket handle
         * @return string the received data as a trimed string
         */
        protected function receive(&$handle)
        {
            $response = '';
            while($tmp = trim(fgets($handle, 513)))
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
         * parses $response and checks the $code
         *
         * @static
         * @access protected
         * @param string $response the response data
         * @param code $code the needed code
         * @return bool true if the code matches, otherwise false
         */
        protected function parse($response, $code)
        {
            $responsecode = mb_substr($response, 0, 3);
            if (trim($responsecode) == trim($code))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * a simple wrapper of smtp_mail()
         *
         * @static
         * @access public
         * @param string $to the email target
         * @param string $from the sender
         * @param string $subject the email subject
         * @param string $text the content
         * @param string $charset the charset (default: UTF-8)
         * @return int false if $to or $from are no valid email addresses, otherwise the return code of smtp_mail()
         */
        public function sendmail($to, $from, $subject, $text, $charset = 'UTF-8')
        {
            if (!Text::is_email($to) || !Text::is_email($from))
            {
                return false;
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