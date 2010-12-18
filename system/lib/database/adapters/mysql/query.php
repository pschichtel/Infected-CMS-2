<?php
    /**
     *
     */
    class mysqlQuery implements IDatabaseQuery
    {
        private $query;

        public function __construct()
        {
            $this->query = '';
        }

        public function insert_into($table, $fields, $data)
        {
            $this->query = 'INSERT INTO `' . $table . '` (`' . implode('`,`', $fields) . '`) VALUES (' . implode(',', $data) . ') ';
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
            return $this;
        }

        public function delete_from($table)
        {
            $this->query = 'DELETE FROM `' . $table . '` ';
            return $this;
        }

        public function update($table, $fields, $data)
        {
            $limit = min(count($fields), count($data));
            $setter = '';
            for ($i = 0; $i < $limit; $i++)
            {
                $setter .= ',`' . $fields[$i] . '`=' . $data[$i];
            }
            $setter[0] = ' ';
            $this->query = 'UPDATE `' . $table . '` SET' . $setter . ' ';
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
    }
?>
