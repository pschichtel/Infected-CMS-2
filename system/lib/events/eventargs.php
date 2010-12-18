<?php
    /**
     *
     */
    class EventArgs
    {
        protected $args;
        
        public static function factory($args)
        {
            if (is_array($args))
            {
                return new self($args);
            }
        }

        private function __construct($args)
        {
            $this->args = $args;
        }

        public function __get($name)
        {
            if (isset($this->args[$name]))
            {
                return $this->args[$name];
            }
        }

        public function __isset($name)
        {
            return isset($this->args[$name]);
        }
    }
?>
