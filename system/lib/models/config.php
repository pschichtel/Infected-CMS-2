<?php
    /**
     * 
     */
    class Config
    {
        /**
         * the database connection to fecth the config from
         *
         * @access private
         * @var Database
         */
        private $db;

        /**
         * the config array
         *
         * @access private
         * @var array
         */
        private static $config = array();
        
        /**
         * the config log object
         *
         * @access private
         * @var Log
         */
        private $log;

        /**
         * initiates the Config object and fetches the configuration
         *
         * @access private
         * @global Database $db
         */
        private function __construct($name)
        {
            //$this->db = &FrontController::globalDatabase();
            //$this->log = new Log('config');
            $this->fetch($name);
        }

        private function fetch()
        {
            if (isset(self::$config[$name]))
            {
                return;
            }
            else
            {
                //Config-Einträge für $name aus der Datenbank laden
                self::$config['core']['defaultpage'] = 'test';
            }
        }
    }
?>
