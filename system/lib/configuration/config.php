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
        private $config;
        
        /**
         * the config log object
         *
         * @access private
         * @var Log
         */
        private $log;

        /**
         * golds the instance of the Config class
         *
         * @static
         * @access private
         * @var Config the Config object
         */
        private static $instance = null;

        /**
         * initiates the Config object and fetches the configuration
         *
         * @access private
         * @global Database $db
         */
        private function __construct()
        {
            $this->db = &FrontController::globalDatabase();
            $this->log = new Log('config');
            $this->log->write(1, 'init', 'No params needed, fetching config from Database');
            $this->fetch();
        }

        /**
         * destructs the object
         */
        public function __destruct()
        {
            unset($this->config);
            unset($this->log);
        }

        /**
         * returns the requested config entry or triggers an error if the entry does not exist
         *
         * @access public
         * @param string $name the name of the entry
         * @return mixed the value of the entry or null if the entry does not exist
         */
        public function __get($name)
        {
            if (isset($this->config[$name]))
            {
                $this->log->write(2, 'info', "Entry found: name: $name,value: {$this->config[$name]}");
                return $this->config[$name];
            }
            else
            {
                $this->log->write(1, 'error', 'Undefined property "Config::' . $name . '"');
                throw new ConfigException('Undefined property "Config::' . $name . '"');
                return null;
            }
        }

        /**
         * checks whether an config entry exists
         *
         * @access public
         * @param string $name the name of the entry
         * @return bool true if it exists or false if not
         */
        public function __isset($name)
        {
            return isset($this->config[$name]);
        }

        /**
         * disables cloning of the class
         *
         * @access private
         */
        private function __clone()
        {}

        /**
         * fetches the configuration from the database
         *
         * @access public
         */
        public function fetch()
        {
            $this->config = array();
            $query = 'SELECT `index`,`value` FROM `PREFIX_config`';
            $result;
            try
            {
                $result = $this->db->getData($query);
            }
            catch(DBException $e)
            {
                trigger_error('Config could not be fetched!', E_USER_ERROR);
                Debug::log_error('exception', $e->getMessage());
            }
            
            foreach ($result as $row)
            {
                if (is_numeric($row->value))
                {
                    if (mb_substr_count($row->value, '.') < 1)
                    {
                        $row->value = (int) $row->value;
                    }
                    else
                    {
                        $row->value = (double) $row->value;
                    }
                    $this->config[$row->index] = $row->value;
                    continue;
                }
                $this->config[$row->index] = $row->value;
            }
            $this->log->write(2, 'info', 'Config fetched: ' . count($this->config) . ' entries');
        }

        /**
         * returns an instance of the Config class
         *
         * @static
         * @access public
         * @return &Config
         */
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
