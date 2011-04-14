<?php
    interface IConfig
    {
        public function exists($name);
        public function get($name, $default = null);
        public function set($name, $value, $overwrite = true);
        public function getAll();
        public function setMultiple(array $data);
        public function setConfig(array $config);
    }
?>
