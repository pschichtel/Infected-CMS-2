<?php
    abstract class Autoloader
    {
        protected static $classmap = array();
        
        public static function register($callback = null)
        {
            if ($callback !== null)
            {
                if (is_callable($callback))
                {
                    spl_autoload_register($callback);
                }
                else
                {
                    throw new Exception('Autoloader::register(1): invalid callback given!');
                }
            }
            else
            {
                spl_autoload_register(array('Autoloader', 'autoload'));
            }
        }

        public static function unregister($callback)
        {
            return spl_autoload_unregister($callback);
        }

        public static function autoload($name)
        {
            if (isset(self::$classmap[$name]))
            {
                require_once ICMS_SYS_PATH . self::$classmap[$name];
            }
        }

        public static function addClassMap(array $map = null)
        {
            static $classmapLoaded = false;
            if ($map !== null)
            {
                self::$classmap = array_merge(self::$classmap, $map);
            }
            elseif (!$classmapLoaded && file_exists(ICMS_SYS_PATH . 'lib/classmap.php'))
            {
                $map = include_once ICMS_SYS_PATH . 'lib/classmap.php';
                self::$classmap = array_merge(self::$classmap, $map);
                $classmapLoaded = true;

            }
            else
            {
                return false;
            }
        }
    }
?>
