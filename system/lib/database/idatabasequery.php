<?php
    /**
     *
     */
    interface IDatabaseQuery
    {
        public function insert_into($table, $fields, $data);
        public function select_from($table, $fields);
        public function delete_from($table);
        public function update($table, $fields, $data);

        public function condition($condition);
        public function orderBy($field, $asc = true);

        public function custom($command);
        public function clear();

        public function getQuery();
        public function expectsResult($expects = null);
    }
?>
