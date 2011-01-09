<?php
    /**
     *
     */
    interface IConfigFile
    {
        public function save();
        public function get($name);
        public function set($name, $value, $dontoverwrite = false);
        public function exists($name);
        public function setMultiple(array $data);
        public function setConfig(array $config);
        public function getAll();
    }
?>
