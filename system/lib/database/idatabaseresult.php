<?php

    /**
     * Interface which should be implemented by database result classes
     */
    interface IDatabaseResult
    {
        public function __construct($result);
        public function __destruct();

        public function fetch($object = false);
        public function fetchAll($objects = false);
        public function numRows();
        public function numFields();
    }

?>
