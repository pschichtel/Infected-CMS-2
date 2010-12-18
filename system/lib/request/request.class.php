<?php

    /**
     * Dependencies
     */
    require_once ICMS_SYS_PATH . 'request/router.php';

    /**
     *
     */
    class Request
    {
        private static $instance = null;


        private function __construct()
        {}

        public function __destruct()
        {}

        private function __clone()
        {}

        public static function &getInstance()
        {
            if (self::$instance === null)
            {
                self::$instance = new self();
            }
            return self::$instance;
        }
    }
    
?>
