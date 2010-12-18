<?php
    /**
     *
     */
    abstract class Registry
    {
        /**
         * Holds the registry entries
         *
         * @static
         * @access private
         * @var mixed[] the registry
         */
        private static $registry = array();

        /**
         * checks whether the given index is set
         *
         * @static
         * @access public
         * @param string $name the name
         * @return bool true if it exists
         */
        public static function has($name)
        {
            return isset(self::$registry[$name]);
        }

        /**
         * creates an entry
         *
         * @static
         * @access public
         * @param string $name the name
         * @param mixed $value the value
         * @return void
         */
        public static function set($name, $value)
        {
            if (!self::has($name))
            {
                self::$registry[$name] = $value;
            }
        }

        /**
         * creates an entry or overwrites an existing
         *
         * @static
         * @access public
         * @param string $name the name
         * @param mixed $value the value
         * @return void
         */
        public static function overwrite($name, $value)
        {
            self::$registry[$name] = $value;
        }

        /**
         * gets an entry by reference
         *
         * @static
         * @access public
         * @param string $name the name
         * @return mixed the entry value
         */
        public static function &get($name)
        {
            if (self::has($name))
            {
                return self::$registry[$name];
            }
            return null;
        }

        /**
         * gets the whole registry by copy
         *
         * @static
         * @access public
         * @return mixed[] the registy
         */
        public static function getAll()
        {
            return self::$registry;
        }

        /**
         * deletes an entry
         *
         * @static
         * @access public
         * @param string $name the name
         * @return bool true if an entry was deleted
         */
        public static function delete($name)
        {
            if (self::has($name))
            {
                unset(self::$registry[$name]);
                return true;
            }
            return false;
        }

        /**
         * deletes the whole registry
         *
         * @static
         * @access public
         * @return void
         */
        public static function deleteAll()
        {
            self::$registry = array();
        }

        /**
         * reads an ini file and puts its entries into the registry
         *
         * @static
         * @access public
         * @param string $filepath the path to the ini file
         * @return bool true on success, otherwise false
         */
        public static function readINI($filepath)
        {
            if (file_exists($filepath))
            {
                $ini = parse_ini_file($filename);
                if (is_array($ini))
                {
                    foreach ($ini as $name => $value)
                    {
                        self::set($name, $value);
                    }
                    return true;
                }
            }
            return false;
        }
    }
?>
