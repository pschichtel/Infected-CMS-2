<?php
    /**
     *
     */
    interface IConfigFile extends IConfig
    {
        public function save();
        public function load();
    }
?>
