<?php
    /**
     *
     */
    class StaticRoute
    {
        private $route;
        private $controller;
        private $action;
        private $valiidators;

        public function __construct($route, $controller, $action, $validators = array())
        {
            $this->route = $route;
            $this->controller = $controller;
            $this->action = $action;
            $this->valiidators = $validators;
        }
    }
?>
