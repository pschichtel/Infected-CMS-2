<?php
    /**
     *
     */
    class Session
    {
        private static $instance = null;
        private static $sessionName = 'sessid';
        private static $sessionID = null;
        private static $sessionLifetime = null;

        private $session;

        private function __construct()
        {
            session_name(self::$sessionName);
            if (self::$sessionID !== null)
            {
                session_id(self::$sessionID);
            }
            if (self::$sessionLifetime !== null)
            {
                session_set_cookie_params(self::$sessionLifetime);
            }
            session_start();
            $this->session =& $_SESSION;
        }

        public function __destruct()
        {
            unset($this->session);
        }

        private function __clone()
        {}

        public static function &instance()
        {
            if (self::$instance === null)
            {
                self::$instance = new self();
            }
            return self::$instance;
        }
        
        public static function name($name = null)
        {
            if (!$name || self::$instance !== null)
            {
                return session_name();
            }
            else
            {
                self::$sessionName = strval($name);
            }
        }

        public static function id($id = null)
        {
            if (!$id || self::$instance !== null)
            {
                return session_id();
            }
            else
            {
                self::$sessionID = $id;
            }
        }

        public static function lifetime($lifetime = null)
        {
            if ($lifetime === null || self::$instance !== null)
            {
                return self::$sessionLifetime;
            }
            else
            {
                self::$sessionLifetime = intval($lifetime);
            }
        }

        public static function destroy()
        {
            if (self::$instance !== null)
            {
                session_unset();
                session_destroy();
                $_SESSION = array();
                unset($_SESSION);
                unset(self::$instance);
                self::$instance = null;
            }
        }
        
        public function get($name)
        {
            if ($this->exists($name))
            {
                return $this->session[$name];
            }
            else
            {
                return null;
            }
        }
        
        public function exists($name)
        {
            return isset($this->session[$name]);
        }

        public function set($name, $value)
        {
            $this->session[$name] = $value;
        }
    }
?>
