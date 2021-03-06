<?php
    /**
     * 
     */
    class Log
    {
        /**
         * the loglevel which controls the logger
         *
         * @static
         * @access protected
         * @var int
         */
        protected static $loglevel = 5;

        /**
         *
         * @static
         * @access private
         * @var Log
         */
        private static $instances = array();

        /**
         * the file handle of the log
         *
         * @access protected
         * @var resource
         */
        protected $fhandle;

        /**
         * the name of the log
         *
         * @access protected
         * @var string
         */
        protected $logname;

        /**
         * the path of the lof file
         *
         * @access protected
         * @var string
         */
        protected $filepath;

        /**
         * the file mode the log gets opened with
         *
         * @access protected
         * @var string
         */
        protected $fmode;

        /**
         * true if there was something written to the log file
         *
         * @access protected
         * @var bool
         */
        protected $sthWritten;

        /**
         * initiates the Log object
         *
         * @access public
         * @param string $logname the filename for the log
         */
        private function __construct($logfile)
        {
            if (!is_writable(dirname($logfile)))
            {
                throw new Exception('the logfile is not writable!', 401);
            }
            $this->sthWritten = false;
            $this->filepath = $logfile;
            $this->fmode = 'ab';
        }

        /**
         * closes the log-file
         *
         * @access public
         */
        public function __destruct()
        {
            if ($this->sthWritten)
            {
                $this->write(0, 'Logger', '----------| Log closed |----------');
                @fclose($this->fhandle);
            }
        }

        private function __clone()
        {}

        public static function instance($logfile)
        {
            if (!isset(self::$instances[$logfile]))
            {
                self::$instances[$logfile] = new self($logfile);
            }
            return self::$instances[$logfile];
        }

        protected function open()
        {
            if (!$this->sthWritten)
            {
                $this->sthWritten = true;
                $this->fhandle = @fopen($this->filepath, $this->fmode);
                if ($this->fhandle === false)
                {
                    throw new Exception('Could not open logfile "' . $this->logfile . '" for writing! Check file permissions!');
                }
                $this->write(0, 'Logger', '----------> Log opened <----------');
            }
        }

        /**
         * writes a line into the log file
         *
         * @access public
         * @param int $debugLevel the debug level to print at
         * @param string $entryType the type of the log entry
         * @param string $message the message/text of the entry
         */
        public function write($logLevel, $entryType, $message)
        {
            if (self::$loglevel >= $logLevel)
            {
                $this->open();
                $timestamp = date('d.m.y H:i:s');
                $message = str_replace("\n", ' ', $message);
                $message = str_replace("\r", ' ', $message);
                flock($this->fhandle, LOCK_EX);
                @fwrite($this->fhandle, "[$timestamp][$entryType] $message\n");
                flock($this->fhandle, LOCK_UN);
            }
        }

        public static function logLevel($level = null)
        {
            if ($level === null)
            {
                return self::$loglevel;
            }
            else
            {
                if ($level < 0 || $level > 5)
                {
                    return false;
                }
                else
                {
                    self::$loglevel = $level;
                    return true;
                }
            }
        }
    }
?>
