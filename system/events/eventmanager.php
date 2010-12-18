<?php

    /**
     * Dependencies
     */
    Application::import('events::event');
    Application::import('events::eventargs');
    Application::import('events::ieventhandler');
    Application::import('events::eventhandler');
    Application::import('events::eventhandlerchain');
    
    /**
     * 
     */
    abstract class EventManager
    {
        protected static $events = array();

        /**
         * registers an event in the eventmanagement
         *
         * @access public
         * @static
         * @param Event $event the event to register
         */
        public static function registerEvent(Event $event)
        {
            self::$events[$event->getName()] = $event;
        }

        /**
         * Checks whether the given event is registered
         *
         * @access public
         * @static
         * @param string $eventname the name of the event
         * @return bool true if the event is registered, otherweise false.
         */
        public static function eventRegistered($eventname)
        {
            return isset(self::$events[$eventname]);
        }

        /**
         * adds an eventhandler to an event
         *
         * @access public
         * @static
         * @param string $eventname name of the event
         * @param IEventHandler $eventhandler the eventhandler zu add
         */
        public static function addEventHandler($eventname, IEventHandler $eventhandler)
        {
            self::$events[$eventname]->addHandler($eventhandler);
        }

        /**
         * triggers the given event with the given sender object und event arguments
         *
         * @access public
         * @static
         * @param string $eventname
         * @param Object $sender the object that triggered the event
         * @param EventArgs $eventargs the event arguments for the event
         * @return int 0 if the event succeeded, otherwise > 0
         */
        public static function triggerEvent($eventname, $sender, EventArgs $eventargs)
        {
            if (!self::eventRegistered($eventname))
            {
                return 1;
            }
            return self::$events[$eventname]->trigger($sender, $eventargs);
        }

        /**
         * triggers all registered events at once
         *
         * @todo $eventargs auf sinn prüfen
         * @access public
         * @static
         * @param string $eventname the name of the events //sinn ?
         * @param Object $sender the object that triggered the event
         * @param EventArgs $eventargs the event arguments for the events
         * @return int the amount of failed event handlers
         */
        public static function triggerAllEvents($eventname, $sender, EventArgs $eventargs)
        {
            if (!self::eventRegistered($eventname))
            {
                return 1;
            }
            $failureCount = 0;
            foreach (self::$events as &$event)
            {
                $failed = $event->trigger($sender, $eventargs);
                if ($failed > 0)
                {
                    $failureCount += $failed;
                }
            }
            return $failureCount;
        }
    }
?>
