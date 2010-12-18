<?php
    /**
     *
     */
    class Event
    {
        protected $name;
        protected $handler;
        protected $handlerCount;

        public function __construct($name)
        {
            $this->name = $name;
            $this->handler = array();
            $this->handlerCount = 0;
        }

        public function getName()
        {
            return $this->name;
        }

        public function getHandlerCount()
        {
            return $this->handlerCount;
        }

        public function addHandler(iEventHandler $handler)
        {
            $this->handler[] = $handler;
            $this->handlerCount++;
        }

        public function trigger($sender, EventArgs $eventargs)
        {
            $failureCount = 0;
            for ($i = 0; $i < $this->handlerCount; $i++)
            {
                if (!$this->handler[$i]->trigger($sender, $eventargs))
                {
                    $failureCount++;
                }
            }
            return $failureCount;
        }
    }
?>
