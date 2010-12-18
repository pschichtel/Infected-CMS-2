<?php

    /**
     *
     */
    class mysqlResult implements IDatabaseResult
    {
        private $result;

        public function __construct($result)
        {
            if (!is_resource($result))
            {
                throw new DatabaseException('No valid result given!', 504);
            }
            $this->result = $result;
        }

        public function __destruct()
        {
            mysql_free_result($this->result);
        }

        public function fetch($object = false)
        {
            $row = mysql_fetch_array($this->result, MYSQL_ASSOC);
            if (!$row)
            {
                return false;
            }
            else
            {
                return ($object ? ((object)$row) : $row);
            }
        }

        public function fetchAll($objects = false)
        {
            $rows = array();
            while (($row = $this->fetch($objects)) !== false)
            {
                $rows[] = $row;
            }
            return $rows;
        }

        public function numRows()
        {
            return mysql_num_rows($this->result);
        }

        public function numFields()
        {
            return mysql_num_fields($this->result);
        }
    }
?>
