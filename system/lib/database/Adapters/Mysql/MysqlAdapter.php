<?php

    /**
     * 
     */
    class MysqlAdapter implements IDatabaseAdapter
    {
        private $host;
        private $user;
        private $pass;
        private $dbname;
        private $charset;

        private $dbhandle;
        private $connected;


        public static function validate($data)
        {
            if (
                (isset($data['host'])) &&
                 isset($data['user']) &&
                 isset($data['path']) &&
                 preg_match('/^\/[\w\d]+$/i', $data['path'])
            )
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        public function QueryBuilder()
        {
            $this->connect();
            return new MysqlQuery($this->dbhandle);
        }
        
        public function __construct($data)
        {
            $this->host = $data['host'];
            $this->user = $data['user'];
            $this->pass = (isset($data['pass']) ? $data['pass'] : '');
            $this->dbname = substr($data['path'], 1);
            $this->charset = (isset($data['fragment']) ? $data['fragment'] : null);


            $this->dbhandle = null;
            $this->connected = false;
        }

        public function __destruct()
        {
            $this->disconnect();
        }

        public function connect()
        {
            if (!$this->connected)
            {
                $this->dbhandle = mysql_connect($this->host, $this->user, $this->pass);
                if (!is_resource($this->dbhandle))
                {
                    throw new DatabaseException('Connection to the database failed!', 501);
                }
                if (!mysql_select_db($this->dbname, $this->dbhandle))
                {
                    throw new DatabaseException('Database selection failed!', 502);
                }
                $this->connected = true;
                if (!is_null($this->charset))
                {
                    mysql_set_charset($this->charset, $this->dbhandle);
                }
            }
        }

        public function disconnect()
        {
            if ($this->connected)
            {
                mysql_close($this->dbhandle);
                $this->dbhandle = null;
                $this->connected = false;
            }
        }

        public function isConnected()
        {
            return $this->connected;
        }

        public function query(IDatabaseQuery $query)
        {
            $this->connect();
            $result = mysql_query($query->getQuery(), $this->dbhandle);
            if (!is_resource($result) && $query->expectsResult())
            {
                throw new DatabaseException('query failed!', 503);
            }
            elseif (!$query->expectsResult())
            {
                return;
            }
            else
            {
                return new MysqlResult($result);
            }
        }

        public function unbufferedQuery(IDatabaseQuery $query)
        {
            $this->connect();
            $result = mysql_unbuffered_query($query, $this->dbhandle);
            if (!is_resource($result))
            {
                throw new DatabaseException('query failed!', 503);
            }
            return new mysqlResult($result);
        }

        public function lastError()
        {
            return mysql_error($this->dbhandle);
        }

        public function lastInsertedRowid()
        {
            return mysql_insert_id($this->dbhandle);
        }
    }

?>
