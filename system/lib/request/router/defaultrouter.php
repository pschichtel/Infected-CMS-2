<?php
    /**
     *
     */
    class DefaultRouter implements IRouter
    {
        private $controller;
        private $action;
        private $params;

        public function  __construct()
        {
            $this->controller = '';
            $this->action = '';
            $this->params = array();
        }

        public function resolveRoute(IRequest $request)
        {
            if ($request->issetGET('site'))
            {
                $this->controller = $request->getGET('site');
            }
            if ($request->issetGET('do'))
            {
                $this->action = $request->getGET('do');
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
