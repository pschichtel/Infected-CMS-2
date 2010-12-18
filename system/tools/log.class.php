<?php
    /**
     * 
     */
    class Log
    {
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
         * defines whether to print a backtrace
         *
         * @access protected
         * @var bool true for backtracing
         */
        protected $backtrace;

        /**
         * initiates the Log object
         *
         * @access public
         * @param string $logname the filename for the log
         */
        public function __construct($logname, $backtrace = false)
        {
            $this->sthWritten = false;
            $this->backtrace = $backtrace;
            $this->logname = $logname;
            $this->filepath = CI_APP_PATH . 'logs' . DIRECTORY_SEPARATOR . $logname . '.log';
            $this->fmode = 'a';
            if (!file_exists($this->filepath) || filesize($this->filepath) > CoreConfig::LOG_LIMIT)
            {
                $this->fmode = 'w';
            }
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
                $this->write(0, 'info', '----------| Log closed |----------');
                @fclose($this->fhandle);
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
        public function write($debugLevel, $entryType, $message)
        {
            if (CoreConfig::DEBUG >= $debugLevel)
            {
                if (!$this->sthWritten)
                {
                    $this->sthWritten = true;
                    $this->fhandle = @fopen($this->filepath, $this->fmode);
                    if ($this->fhandle === false)
                    {
                        throw new IOException('Could not open logfile "' . $this->logname . '.log.txt" for writing! Check file permissions!');
                    }
                    $this->write(0, 'info', '----------> Log opened <----------');
                }
                $timestamp = date('d.m. H:i:s');
                $message = str_replace("\n", ' ', $message);
                $message = str_replace("\r", ' ', $message);
                @fwrite($this->fhandle, "[$timestamp][$entryType] $message\n");
                if ($this->backtrace)
                {
                    ob_start();
                    debug_print_backtrace();
                    $trace = ob_get_clean();

                    @fwrite($this->fhandle, $trace . "\n=================\n\n");
                }
            }
        }
    }
?>
