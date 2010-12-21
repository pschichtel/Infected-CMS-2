<?php
    class Router
    {
        protected $request;
        protected $controller;
        protected $action;
        protected $params;
        protected $filename;

        public function __construct(Request& $request)
        {
            $this->request =& $request;

            if ($this->request->getModRewrite())
            {
                $this->parseModRewrite($this->request->getRequestUri());
            }
            else
            {
                $this->parse($this->request->getRequestUri());
            }
        }

        //public function parse()
        public function parseModRewrite($query)
        {
            $this->controller = '';
            $this->action = '';
            $this->params = array();
            
            echo "Query: $query\n";



            $lastSlash = strrpos($query, '/');
            $filename = substr($query, $lastSlash + 1);
            if (preg_match('/\./', $filename))
            {
                $this->filename = $filename;
            }


            $parts = explode('/', $query);
            $count = count($parts);
            if ($count == 1)
            {
                $this->controller = $parts[0];
                return true;
            }
            if ($count % 2 != 0)
            {
                if (preg_match('/\./', $parts[$count - 1]))
                {
                    $count--;
                    $this->filename = $parts[$count];
                    unset($parts[$count]);
                }
            }

            if ($count < 2)
            {
                return false;
            }

            $this->controller = $parts[0];
            $this->action = $parts[1];

            for ($i = 2; $i < $count; $i += 2)
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

        public function getParam($name)
        {
            if (isset($this->params[$name]))
            {
                return $this->params[$name];
            }
            else
            {
                return null;
            }
        }

        public function getParams()
        {
            return $this->params;
        }

        public function getFilename()
        {
            return $this->filename;
        }
    }
?>
