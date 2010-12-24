<?php
    /**
     *
     */
    interface IRequest
    {
        public function getAction();
        public function getController();

        public function getAll($type);
        public function get($type, $name);
        public function exists($type, $name);
        public function getRequestUri();
        public function getModRewrite();

        public function route(IRouter $router);
    }
?>
