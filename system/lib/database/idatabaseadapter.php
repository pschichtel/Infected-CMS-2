<?php
    /**
     * Interface which must be implemented by every database adapter
     */
    interface IDatabaseAdapter
    {
        public static function validate($data);
        public function QueryBuilder();

        public function __construct($data);
        public function __destruct();

        public function connect();
        public function disconnect();
        public function isConnected();

        public function query(IDatabaseQuery $query);
        public function unbufferedQuery(IDatabaseQuery $query);

        public function lastError();
        public function lastInsertedRowid();
    }
?>
