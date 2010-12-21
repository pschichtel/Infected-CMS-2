<?php
    /**
     *
     */
    class mysqlQuery implements IDatabaseQuery
    {
        private $query;
        private $dbhandle;
        private $expectsResult;

        public function __construct(&$dbhandle)
        {
            $this->query = '';
            $this->expectsResult = false;
            $this->dbhandle = &$dbhandle;
        }

        public function escape($string)
        {
            return mysql_real_escape_string($string, $this->dbhandle);
        }

        public function insert_into($table, $fields, $data)
        {
            $data = array_map(array($this, 'escape'), $data);
            $this->query = 'INSERT INTO `' . $table . '` (`' . implode('`,`', $fields) . '`) VALUES (' . implode(',', $data) . ') ';
            $this->expectsResult = false;
            return $this;
        }

        public function select_from($table, $fields = null)
        {
            if (is_null($fields))
            {
                $fields = '*';
            }
            else
            {
                $fields = '`' . implode('`,`', $fields) . '`';
            }
            $this->query = 'SELECT ' . $fields . 'FROM `' . $table . '` ';
            $this->expectsResult = true;
            return $this;
        }

        public function delete_from($table)
        {
            $this->query = 'DELETE FROM `' . $table . '` ';
            $this->expectsResult = false;
            return $this;
        }

        public function update($table, $fields, $data)
        {
            $limit = min(count($fields), count($data));
            $data = array_map(array($this, 'escape'), $data);
            $setter = '';
            for ($i = 0; $i < $limit; $i++)
            {
                $setter .= ',`' . $fields[$i] . '`=' . $data[$i];
            }
            $setter[0] = ' ';
            $this->query = 'UPDATE `' . $table . '` SET' . $setter . ' ';
            $this->expectsResult = false;
            return $this;
        }

        public function condition($condition)
        {
            $this->query .= 'WHERE ' . $condition . ' ';
            return $this;
        }

        public function orderBy($field, $asc = true)
        {
            $this->query .= 'ORDER BY `' . $field . '` ' . ($asc ? 'ASC' : 'DESC');
            return $this;
        }

        public function custom($command)
        {
            $this->query .= $command . ' ';
            return $this;
        }

        public function getQuery()
        {
            return trim($this->query);
        }

        public function expectsResult($expects = null)
        {
            if ($expects === null)
            {
                return $this->expectsResult;
            }
            else
            {
                $this->expectsResult = (bool)$expects;
                return $this;
                
            }

        }
    }
?>
