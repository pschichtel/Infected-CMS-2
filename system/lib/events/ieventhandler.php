<?php
    /**
     *
     */
    interface IEventHandler
    {
        public function trigger($sender, EventArgs $eventargs);
    }
?>
