<?php
    /**
     *
     */
    abstract class AbstractDatabaseResult
    {
        public static abstract function factory($result);

        public abstract function fetch($array = true);
         
        public abstract function fetchAll($array = true);

        public abstract function fetchArray();

        public abstract function fetchObject();

        public abstract function numRows();
    }

?>
