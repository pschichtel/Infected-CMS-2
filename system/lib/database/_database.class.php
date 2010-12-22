<?php
    /**
     * 
     */
    class _Database
    {
        /**
         * the database handle
         *
         * @access private
         * @var resource
         */
        private $dbhandle;

        /**
         * the host of the database
         *
         * @access private
         * @var string
         */
        private $host;

        /**
         * the username
         *
         * @access private
         * @var string
         */
        private $user;

        /**
         * the password
         *
         * @access private
         * @var string
         */
        private $pass;

        /**
         * the name of the database
         *
         * @access private
         * @var string
         */
        private $db;

        /**
         * the table prefix
         *
         * @access private
         * @var string
         */
        private $prefix;

        /**
         * whether to use UTF-8 or not
         *
         * @access private
         * @var bool
         */
        private $utf8;

        /**
         * the database Log object
         *
         * @access private
         * @var Log
         */
        private $log;

        /**
         * whether to class is connected to the database or not
         *
         * @access public
         * @var bool
         */
        public $connected;

        /**
         * the affected rows of the last statement
         *
         * @access public
         * @var int
         */
        public $affected_rows;

        /**
         * initiates the Database class with the given data.
         * throws a DBException on failure
         *
         * @access public
         * @param string $host MySQL serve address
         * @param string $user username
         * @param string $pass user pasword
         * @param string $db database name
         * @param string $prefix table prefix
         * @param bool $utf8 to use UTF-8 or not
         */
        public function __construct($host, $user, $pass, $db, $prefix, $utf8 = false)
        {
            $this->log = new Log('database');
            if (!is_string($host) ||
                !is_string($user) ||
                !is_string($pass) ||
                !is_string($db) ||
                !is_bool($utf8))
            {
                throw new DBException('Initialisation failed! Wrong params MySQL::MySQL(string host, string user, string pass, string db, string prefix, bool utf8)');
            }
            $this->log->write(2, 'init', "Contructing Object: host: $host,user: $user,pass:****,db: $db,prefix: $prefix,utf8: " . ($utf8 ? 'true' : 'false'));
            $this->host = $host;
            $this->user = $user;
            $this->pass = $pass;
            $this->db = $db;
            $this->prefix = $prefix;
            $this->utf8 = $utf8;
            $this->connected = false;
        }

        /**
         * destructs the object and closes the database connection if still open.
         *
         * @access public
         */
        public function __destruct()
        {
            $this->log->write(3, 'destruct', 'closing DB-connection...');
            if (is_resource($this->dbhandle))
            {
                mysql_close($this->dbhandle);
            }
            unset($this->log);
        }

        /**
         * etablishes the connection to the database.
         * throws a DBException on failure
         *
         * @access private
         */
        private function connect()
        {
        }

        /**
         * checks whether the connection to the MySQL-server is possible
         *
         * @access public
         * @return bool
         */
        public function checkConnection()
        {
            try
            {
                $this->connect();
            }
            catch (DBException $e)
            {
                return false;
            }
            catch (Exception $e)
            {
                return false;
            }
            return true;
        }

        /**
         * counts the given $table.
         * throws a DBException an failure
         *
         * @access public
         * @param string $table the table name
         * @return int
         */
        public function CountTable($table)
        {
            try
            {
                $this->connect();
            }
            catch (DBException $e)
            {
                throw new DBException('Database::CountData failed to connect! Message: ' . $e->getMessage());
            }
            $query = 'SELECT count(*) AS \'count\' FROM `' . $this->prefix . $table . '`';
            $result = $this->query($query);
            if ($result === false)
            {
                throw new DBException('Database::CountTable failed to count the given table "' . $table . '"! Message: ' . mysql_error());
            }
            $result = mysql_fetch_row($result);
            $this->log->write(2, 'info', "Database::CountTable: table: $table,rows: {$result[0]}");
            return $result[0];
        }

        /**
         * returns the col names of the given $table.
         * throws a DBException on failure
         *
         * @access public
         * @param string $table the table name
         * @return array
         */
        public function GetColNames($table)
        {
            try
            {
                $this->connect();
            }
            catch (DBException $e)
            {
                throw new DBException('Database::GetColNames failed to connect! Message: ' . $e->getMessage());
            }
            $query = 'DESCRIBE `' . $this->prefix . $table . '`';
            $result = $this->GetData($query);
            $colNames = array();
            foreach ($result as $row)
            {
                $colNames[] = $row->Field;
            }
            return $colNames;
        }

        /**
         * returns a string with the last error number an error message
         *
         * @access public
         * @return string
         */
        public function error()
        {
            try
            {
                $this->connect();
            }
            catch (DBException $e)
            {
                throw new DBException('Database::error failed to connect! Message: ' . $e->getMessage());
            }
            $this->log->write(2, 'info', 'Database::Error: errno: ' . mysql_errno($this->dbhandle) . ',message: ' . mysql_error($this->dbhandle));
            return mysql_errno($this->dbhandle) . ': ' . mysql_error($this->dbhandle);
        }

        /*
         * Wrapper functions
         *
         * @access public
         *
         */
        
        public function query($query)
        {
            try
            {
                $this->connect();
            }
            catch (DBException $e)
            {
                throw new DBException('Database::query failed to connect! Message: ' . $e->getMessage());
            }
            $query = preg_replace('/`PREFIX_([\w\d-]+)`/Us', '`' . $this->prefix . '$1`', $query);
            $result = mysql_query($query, $this->dbhandle);
            $this->affected_rows = mysql_affected_rows($this->dbhandle);
            return $result;
        }

        public function escape_string($string)
        {
            try
            {
                $this->connect();
            }
            catch (DBException $e)
            {
                throw new DBException('Database::escape_string failed to connect! Message: ' . $e->getMessage());
            }
            return @mysql_real_escape_string($string, $this->dbhandle);
        }

        public function fetch_array(&$result, $type = null)
        {
            if (!is_resource($result))
            {
                return false;
            }
            if ($type === null)
            {
                return @mysql_fetch_array($result);
            }
            else
            {
                return @mysql_fetch_array($result, $type);
            }
        }

        public function fetch_assoc(&$result)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_fetch_assoc($result);
        }
        
        public function fetch_field(&$result, $offset = 0)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_fetch_field($result, $offset);
        }
        
        public function fetch_lengths(&$result)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_fetch_lengths($result);
        }
        
        public function fetch_object(&$result)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_fetch_object($result);
        }
        
        public function fetch_row(&$result)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_fetch_row($result);
        }
        
        public function field_flags(&$result, $offset)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_field_flags($result, $offset);
        }
        
        public function field_len(&$result, $offset)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_field_len($result, $offset);
        }
        
        public function field_name(&$result, $offset)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_field_name($result, $offset);
        }
        
        public function field_seek(&$result, $offset)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_field_seek($result, $offset);
        }
        
        public function field_table(&$result, $offset)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_field_table($result, $offset);
        }
        
        public function field_type(&$result, $offset)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_field_type($result, $offset);
        }
        
        public function free_result(&$result)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_free_result($result);
        }
        
        public function result(&$result, $offset, $field = null)
        {
            if (!is_resource($result))
            {
                return false;
            }
            if ($field === null)
            {
                return @mysql_result($result, $offset);
            }
            else
            {
                return @mysql_result($result, $offset, $field);
            }
        }
        
        public function num_rows(&$result)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_num_rows($result);
        }
        
        public function num_fields(&$result)
        {
            if (!is_resource($result))
            {
                return false;
            }
            return @mysql_num_fields($result);
        }
    }
?>
