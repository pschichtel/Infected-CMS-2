<?php
    /**
     *
     */
    abstract class AbstractDatabaseAdapter
    {
        public static abstract function factory($data);
        
        public abstract function query($query);

        public abstract function select($table, $cols, $condition = null);

        public abstract function update($table, $updates, $condition = null);

        public abstract function insert($table, $cols, $values);

        public abstract function delete($table, $condition = null);
    }
?>
