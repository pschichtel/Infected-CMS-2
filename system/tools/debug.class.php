<?php
    /**
     * 
     */
    class Debug
    {
        /**
         * prints $message and a backtrace.
         *
         * @access public
         * @static
         * @param string $message the error message to print
         * @param bool $die if true, te scripts ends
         */
        public static function triggerError($message, $die = false)
        {
            $backtrace = debug_backtrace();
            unset($backtrace[0]);
            $counter = count($backtrace);

            echo '<div style="background-color:white;margin-bottom:5px;color:black">';
            echo '<span style="text-decoration: underline;">Error triggered, ' . ($die ? 'script ends here!' : 'but just backtracing ...') . '</span><br>';
            echo 'Message: ' . htmlspecialchars($message) . '<br />';
            echo 'Backtrace:<br />';
            if (DEBUG > 0)
            {
                foreach ($backtrace as $value)
                {
                    $line = 'Level ' . $counter . ': <strong>';
                    $line .= (isset($value['class']) ? $value['class'] . '::' : '');
                    $line .= $value['function'] . '()</strong>';
                    $line .= ' in <strong>' . (isset($value['file']) ? basename($value['file']) : 'Unknown') . '</strong>';
                    $line .= ' on Line <strong>' . (isset($value['line']) ? $value['line'] : '0') . '</strong>';
                    $line .= '<br />';
                    echo $line;
                    $counter--;
                }
            }
            else
            {
                echo 'Debugging is deactivated: backtrace hidden!';
            }
            echo '</div>';
            if ($die)
            {
                die();
            }
        }

        /**
         * a var_dump() wrapper for multible parameters
         *
         * @access public
         * @static
         * @param mixed the variables to dump
         */
        public static function dump_vars()
        {
            $args = func_get_args();
            echo '<pre style="background-color:white;margin-bottom:5px;color:black!important">';
            if (DEBUG > 0)
            {
                ob_start();
                foreach ($args as $arg)
                {
                    var_dump($arg);
                    echo "\n\n";
                }
                echo htmlspecialchars(ob_get_clean());
            }
            else
            {
                echo 'Debugging is deactivated: var-dump hidden!';
            }
            echo '</pre>';
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
        public static function error_handler($errno, $errstr ,$errfile, $errline, $errcontext)
        {
            if (error_reporting() == 0)
            {
                return;
            }
            $errstr = strip_tags($errstr);
            $errfile = (isset($errfile) ? basename($errfile) : '');
            $errline = (isset($errline) ? $errline : '');
            $errcontext = (isset($errcontext) ? $errcontext : '');

            $errortype = '';

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
            self::log_error($errortype, '[' . $errfile . ':' . $errline . '] ' . $errstr);
            if (CoreConfig::DEBUG > 0)
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

        public static function getErrorType($errno)
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
        public static function benchmark($callback, $params = null, &$result = null)
        {
            if (is_callable($callback))
            {
                $start = strtok(microtime(), ' ') + strtok(' ');
                $result = call_user_func_array($callback, $params);
                $end = strtok(microtime(), ' ') + strtok(' ');
                return number_format($end - $start, 6);
            }
            else
            {
                return false;
            }
        }
    }
?>
