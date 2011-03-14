<?php
    /**
     * 
     */
    abstract class Loader
    {
        protected static $classmap = array();
        
        public static function registerAutoloader($callback = null)
        {
            if ($callback !== null)
            {
                if (is_callable($callback))
                {
                    return spl_autoload_register($callback);
                }
                else
                {
                    throw new Exception('Autoloader::register(1): invalid callback given!');
                }
            }
            else
            {
                return spl_autoload_register(array('Loader', 'autoload'));
            }
        }

        public static function unregisterAutoloader($callback = null)
        {
            if ($callback !== null)
            {
                return spl_autoload_unregister($callback);
            }
            else
            {
                return spl_autoload_unregister(array('Loader', 'autoload'));
            }
        }

        public static function autoload($name)
        {
            self::load($name);
        }

        public static function addClassMap(array $map = null)
        {
            static $classmapLoaded = false;
            if ($map !== null)
            {
                self::$classmap = array_merge(self::$classmap, $map);
                return true;
            }
            elseif (!$classmapLoaded && file_exists(ICMS_SYS_PATH . 'lib/classmap.php'))
            {
                $map = include_once ICMS_SYS_PATH . 'lib/classmap.php';
                self::$classmap = array_merge(self::$classmap, $map);
                $classmapLoaded = true;
                return true;

            }
            else
            {
                return false;
            }
        }
        
        public static function addSysDirectoryToMap($path)
        {
            $path = preg_replace(array('/^(\\|\/)/', '/(\\|\/)$/'), '', $path) . '/';
            
            if (!file_exists(ICMS_SYS_PATH . $path))
            {
                throw new Exception('Autoloader::addSysDirectoryToMap(1): Directory not found!');
            }
            
            $dir = opendir(ICMS_SYS_PATH . $path);
            if ($dir === false)
            {
                throw new Exception('Autoloader::addSysDirectoryToMap(1): Directory could not be read!');
            }
            
            $map = array();
            
            while (($entry = readdir($dir)))
            {
                if (is_file(ICMS_SYS_PATH . $path . $entry))
                {
                    $map[basename($entry, '.php')] = $path . $entry;
                }
            }
            closedir($dir);
            
            return self::addClassMap($map);
        }
        
        public static function getClassMap()
        {
            return self::$classmap;
        }
        
        public static function exists($name)
        {
            return isset(self::$classmap[$name]);
        }
        
        public static function load($name)
        {
            if (self::exists($name))
            {
                require_once ICMS_SYS_PATH . self::$classmap[$name];
                return true;
            }
            else
            {
                return false;
            }
        }
    }
?>
