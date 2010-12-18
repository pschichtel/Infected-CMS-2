<?php

    class MySQL extends DatabaseAbstractAdapter
    {
        private function __construct($data)
        {
            
        }

        public static function factory($data)
        {
            if (
                !isset($data['host']) ||
                !isset($data['user']) ||
                !isset($data['database']) ||
                !isset($data[''])
            )
            return new self($data);
        }
    }

?>
