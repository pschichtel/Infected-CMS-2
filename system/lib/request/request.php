<?php

    /**
     * Dependencies
     */
    require_once ICMS_SYS_PATH . 'lib/request/router/router.php';

    /**
     *
     */
    class Request
    {
        private static $instance = null;
        private $controller;
        private $action;
        private $GET;
        private $POST;
        private $COOKIE;
        private $FILES;
        private $modRewrite;
        private $requestUri;


        private function __construct()
        {
            $this->GET = $_GET;
            $_GET = null;
            $this->POST = $_POST;
            $_POST = null;
            $this->COOKIE = $_COOKIE;
            $_COOKIE = null;
            $this->FILES = $_FILES;
            $_FILES = null;
            
            $this->modRewrite = isset($_SERVER['REDIRECT_URL']);
        }

        public function __destruct()
        {}

        private function __clone()
        {}

        public static function &getInstance()
        {
            if (self::$instance === null)
            {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function getController()
        {
            return $this->controller;
        }

        public function getAction()
        {
            return $this->action;
        }

        public function getGET($name)
        {
            if (isset($this->GET[$name]))
            {
                return $this->GET[$name];
            }
        }

        public function issetGET($name)
        {
            return isset($this->GET[$name]);
        }

        public function getPOST($name)
        {
            if (isset($this->POST[$name]))
            {
                return $this->POST[$name];
            }
        }

        public function issetPOST($name)
        {
            return isset($this->POST[$name]);
        }

        public function getCOOKIE($name)
        {
            if (isset($this->COOKIE[$name]))
            {
                return $this->COOKIE[$name];
            }
        }

        public function issetCOOKIE($name)
        {
            return isset($this->COOKIE[$name]);
        }

        public function getFILES($name)
        {
            if (isset($this->FILES[$name]))
            {
                return $this->FILES[$name];
            }
        }

        public function issetFILES($name)
        {
            return isset($this->FILES[$name]);
        }

        public function getRequestUri()
        {
            return $this->requestUri;
        }

        public function getModRewrite()
        {
            return $this->modRewrite;
        }
    }
    
?>
