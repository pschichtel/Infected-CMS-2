<?php
    abstract class Autoloader
    {
        protected static $extension = '.class.php';
        
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
                    throw new CoreException('Autoloader::register: invalid callback given!');
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
            spl_autoload($name, self::$extension);
        }

        public static function setExtension($extension)
        {
            if (!preg_match('/^\./', $extension))
            {
                $extension = '.' . $extension;
            }
            self::$extension = $extension;
        }

        public static function getExtension($extension)
        {
            return self::$extension;
        }
    }
?>
