<?php
    /**
     * 
     */
    class Config
    {
        private static $instance = null;
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
        private $config = array();
        
        /**
         * the config log object
         *
         * @access private
         * @var Log
         */
        //private $log;

        /**
         * initiates the Config object and fetches the configuration
         *
         * @access private
         * @global Database $db
         */
        private function __construct()
        {
            if (Registry::exists('database'))
            {
                $this->db = Registry::get('database');
            }
            else
            {
                throw new ModelException('[Config::__construct] No database object found!', 401);
            }
            //$this->log = new Log('config');
            $this->fetch('core');
        }

        private function  __clone()
        {}

        public static function &instance()
        {
            if (self::$instance === null)
            {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function fetch($cat)
        {
            if ($this->exists($cat))
            {
                return;
            }
            else
            {
                //Config-Einträge für $name aus der Datenbank laden
                //wenn keine einträge für cat existieren, leeres array setzen
                $this->config[$cat]['defaultpage'] = 'test';
                $this->config[$cat]['defaultdesign'] = 'default';
            }
        }

        public function get($cat, $name)
        {
            if (!$this->exists($cat))
            {
                $this->fetch($cat);
            }
            if (!$this->exists($cat, $name))
            {
                return null;
            }
            return $this->config[$cat][$name];
        }

        public function exists($cat, $name = null)
        {
            if ($name === null)
            {
                return isset($this->config[$cat]);
            }
            else
            {
                return isset($this->config[$cat][$name]);
            }
        }
    }
?>
