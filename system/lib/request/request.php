<?php

    /**
     * Dependencies
     */
    require_once dirname(__FILE__) . '/router/irouter.php';
    require_once dirname(__FILE__) . '/router/defaultrouter.php';
    require_once dirname(__FILE__) . '/irequest.php';
    require_once dirname(__FILE__) . '/requestexception.php';

    /**
     *
     */
    class Request implements IRequest
    {
        private static $instance = null;
        private $controller;
        private $action;
        private $modRewrite;
        private $requestUri;
        private $requestVars;


        private function __construct()
        {
            $this->requestVars = array();
            $this->requestVars['get'] = $_GET;
            $this->requestVars['post'] = $_POST;
            $this->requestVars['cookie'] = $_COOKIE;
            $this->requestVars['files'] = $_FILES;

            $this->requestUri = $_SERVER['REQUEST_URI'];
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

        public function get($type, $name)
        {
            $type = mb_strtolower($type);
            if ($this->exists($type, $name))
            {
                return $this->requestVars[$type][$name];
            }
            else
            {
                return null;
            }
        }

        public function getAll($type)
        {
            if (isset($this->requestVars[$type]))
            {
                return $this->requestVars[$type];
            }
            else
            {
                return null;
            }

        }

        public function exists($type, $name)
        {
            return isset($this->requestVars[$type][$name]);
        }


        public function getRequestUri()
        {
            return $this->requestUri;
        }

        public function getModRewrite()
        {
            return $this->modRewrite;
        }

        public function route(IRouter $router = null)
        {
            if (!$router instanceof IRouter)
            {
                $router = new DefaultRouter();
            }
            if (!$router->resolveRoute($this))
            {
                throw new RequestException('Request::route: resolving the route failed!', 503);
            }

            $this->controller = $router->getController();
            $this->action = $router->getAction();
            $this->requestVars['get'] = array_merge($this->requestVars['get'], $router->getParams());
        }
    }
    
?>
