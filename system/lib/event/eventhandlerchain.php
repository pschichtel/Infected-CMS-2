<?php
    /**
     *
     */
    class EventHandlerChain implements IEventHandler
    {
        protected $eventhandlers;
        protected $count;

        public function __construct()
        {
            $this->eventhandlers = array();
        }
        
        public function add(iEventHandler $eventhandler)
        {
            $this->eventhandlers[] = $eventhandler;
            $this->count++;
        }
        
        public function trigger($sender, EventArgs $eventargs)
        {
            for ($i = 0; $i < $this->count; $i++)
            {
                $this->eventhandlers[$i]->trigger($sender, $eventargs);
            }
        }

        public function eventHandlerExists(iEventHandler $eventhandler)
        {
            for ($i = 0; $i < $this->count; $i++)
            {
                if ($this->eventhandlers[$i] == $eventhandler)
                {
                    return true;
                }
            }
            return false;
        }
    }
?>
