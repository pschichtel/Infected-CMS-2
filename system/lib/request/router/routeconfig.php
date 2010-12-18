<?php
    /**
     *
     */
    final class RouteConfig
    {
        private $paramDelimiter;

        private $nameValueDelimiter;

        private $maxParams;

        private $pageControllerPath;

        public function __construct($pageControllerPath, $paramDelimiter, $nameValueDelimiter, $maxParams = -1)
        {
            $this->pageControllerPath = $pageControllerPath;
            $this->paramDelimiter = $paramDelimiter;
            $this->nameValueDelimiter = $nameValueDelimiter;
            $this->maxParams = $maxParams;
        }

        public function __get($name)
        {
            if (isset($this->{$name}))
            {
                return $this->{$name};
            }
            else
            {
                throw new Exception('RouteConfig has no member "' . $name . '"!');
            }
        }
    }
?>
