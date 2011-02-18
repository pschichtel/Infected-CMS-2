<?php
    /**
     * 
     */
    class EventHandler implements IEventHandler
    {
        protected $handler;

        public static function factory($handler)
        {
            if (is_callable($handler))
            {
                return new self($handler);
            }
        }

        private function __construct($handler)
        {
            $this->handler = $handler;
        }

        public function trigger($sender, EventArgs $eventargs)
        {
            if (is_object($sender) || $sender === null)
            {
                $params = array(&$sender, &$eventargs);
                call_user_func_array($this->handler, $params);
                return true;
            }
            return false;
        }
    }
?>
