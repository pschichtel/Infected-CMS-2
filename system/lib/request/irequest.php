<?php
    /**
     *
     */
    interface IRequest
    {
        public function getAction();
        public function getController();
        public function getGET($name);
        public function getPOST($name);
        public function getCOOKIE($name);
        public function getFILES($name);
        public function issetGET($name);
        public function issetPOST($name);
        public function issetCOOKIE($name);
        public function issetFILES($name);
        public function route(IRouter $router);
    }
?>
