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
            unset(self::$instance);
            self::$instance = null;
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
        
        public static function setName($name)
        {
            if (self::$instance === null)
            {
                self::$sessionName = strval($name);
            }
        }
        
        public static function getName()
        {
            if (self::$instance !== null)
            {
                return session_name();
            }
            else
            {
                return self::$sessionName;
            }
        }
        
        public static function setId($id)
        {
            if (self::$instance === null)
            {
                self::$sessionID = $id;
            }
        }

        public function getId()
        {
            if (self::$instance !== null)
            {
                return session_id();
            }
            else
            {
                return self::$sessionID;
            }
        }
        
        public static function setLifetime($lifetime)
        {
            if (self::$instance === null)
            {
                self::$sessionLifetime = intval($lifetime);
            }
        }

        public static function getLifetime()
        {
            if (self::$instance !== null)
            {
                $sessCookieParams = session_get_cookie_params();
                return $sessCookieParams['lifetime'];
            }
            else
            {
                return self::$sessionLifetime;
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
        
        public function get($name, $default = null)
        {
            if ($this->exists($name))
            {
                return $this->session[$name];
            }
            else
            {
                return $default;
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
