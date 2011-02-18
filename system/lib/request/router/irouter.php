<?php
    /**
     *
     */
    interface IRouter
    {
        public function __construct();
        public function getParams();
        public function getAction();
        public function getController();

        public function resolveRoute(IRequest $request);
        public function addStaticRoute(StaticRoute $route);
    }
?>
