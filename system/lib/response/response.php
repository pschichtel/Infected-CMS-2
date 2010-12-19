<?php
    /**
     *
     */
    class Response
    {
        private static $instance;

        private function __construct()
        {}

        public function __destruct()
        {}

        private function  __clone()
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
