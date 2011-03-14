<?php
    /**
     * 
     */
    class Debug
    {
        /**
         * a var_dump() wrapper for multible parameters
         *
         * @access public
         * @static
         * @param mixed the variables to dump
         */
        public static function var_dump()
        {
            if (Registry::get('debug.allow', false))
            {
                $args = func_get_args();
                echo '<pre>';
                call_user_func_array('var_dump', $args);
                echo '</pre>';
            }
            else
            {
                echo 'Debugging is deactivated: var_dump hidden!';
            }
        }

        /**
         * teh error handler
         *
         * @access public
         * @static
         * @param int $errno
         * @param string $errstr
         * @param string $errfile
         * @param int $errline
         * @param array $errcontext
         */
        public static function error_handler($errno, $errstr, $errfile, $errline, $errcontext)
        {
            if (error_reporting() == 0)
            {
                return;
            }
            $errstr = strip_tags($errstr);
            $errfile = (isset($errfile) ? basename($errfile) : 'unknown');
            $errline = (isset($errline) ? $errline : 'unknown');

            $errortype = self::getErrorTypeStr($errno);
            
            self::log_error($errortype, '[' . $errfile . ':' . $errline . '] ' . $errstr);
            if (Registry::get('debug.printErrors', false))
            {
                echo "$errortype occurrered in [$errfile:$errline]:<br />Message: $errstr<br />";
            }
        }

        /**
         * the exception handler
         *
         * @access public
         * @static
         * @param Exception $e
         */
        public static function exception_handler($e)
        {
            $type = get_class($e);
            self::log_error($type, '[' . basename($e->getFile()) . ':' . $e->getLine() . '] ' . $e->getMessage());

            echo 'An uncaught ' . $type . ' occurred!<br />Message: ' . (DEBUG > 0 ? $e->getMessage() : 'DEBUG-Mode is disabled, check the error-logfile for informations!');
        }

        /**
         * writes $text into the error log file
         *
         * @access public
         * @static
         * @param string $text the text to write in the log file
         * @todo use logger
         */
        public static function log_error($type, $text)
        {
            $logfile = 'include/logs/error.log.txt';
            $handle = fopen($logfile, 'a');
            $text = str_replace("\n", ' ', $text);
            $text = str_replace("\r", ' ', $text);
            $text = strip_tags($text);
            @fwrite($handle,'[' . date('d.m. H:i:s') . '][' . MODE . '][' . $type . ']' . $text . "\n");
            @fclose($handle);
        }

        public static function print_error($type, $message)
        {

        }

        public static function getErrorTypeStr($errno)
        {
            switch ($errno)
            {
                case E_ERROR:
                    $errortype = 'error';
                    break;
                case E_WARNING:
                    $errortype = 'warning';
                    break;
                case E_NOTICE:
                    $errortype = 'notice';
                    break;
                case E_STRICT:
                    $errortype = 'strict';
                    break;
                case E_DEPRECATED:
                    $errortype = 'deprecated';
                    break;
                case E_RECOVERABLE_ERROR:
                    $errortype = 'recoverable error';
                    break;
                case E_USER_ERROR:
                    $errortype = 'usererror';
                    break;
                case E_USER_WARNING:
                    $errortype = 'user warning';
                    break;
                case E_USER_NOTICE:
                    $errortype = 'user notice';
                    break;
                case E_USER_DEPRECATED:
                    $errortype = 'user deprecated';
                    break;
                default:
                    $errortype = 'unknown';
            }
            return $errortype;
        }

        /**
         * scales the runtime of the given callback
         *
         * @static
         * @access public
         * @param callback the funktion to scale
         * @param mixed[] the params to pass to the callback
         * @param &mixed the result of the callback
         * @return mixed the runtime or false on failure
         */
        public static function benchmark($callback, $params = array(), &$result = null)
        {
            if (is_callable($callback))
            {
                $start = microtime(true);
                $result = call_user_func_array($callback, $params);
                $end = microtime(true);
                return ($end - $start);
            }
            else
            {
                return false;
            }
        }
    }
?>
