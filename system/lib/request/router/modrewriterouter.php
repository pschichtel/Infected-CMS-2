<?php
    /**
     * 
     */
    class ModRewriteRouter implements IRouter
    {
        protected $request;
        protected $controller;
        protected $action;
        protected $params;

        public function __construct()
        {
            $this->controller = '';
            $this->action = '';
            $this->params = array();
        }

        /**
         * routes the given path/query to an controller and hist action
         *
         * @access public
         * @param string $query the query to route
         * @return bool false on failure
         */
        public function resolveRoute(IRequest $request)
        {
            if (!$request->exists('get', 'query'))
            {
                return false;
            }
            $query = $request->get('get', 'query');
            $lastSlash = strrpos($query, '/');
            $filename = substr($query, $lastSlash + 1);


            $parts = explode('/', $query);
            $count = count($parts);
            if ($count == 1)
            {
                $this->controller = $parts[0];
                return true;
            }

            $this->controller = $parts[0];
            $this->action = $parts[1];

            for ($i = 2; $i + 1 < $count; $i += 2)
            {
                $this->params[$parts[$i]] = $parts[$i + 1];
            }

            return true;
        }

        public function getController()
        {
            return $this->controller;
        }

        public function getAction()
        {
            return $this->action;
        }

        public function getParams()
        {
            return $this->params;
        }

        public function addStaticRoute(StaticRoute $route)
        {

        }
    }
?>
