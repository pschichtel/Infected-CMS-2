<?php
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'abstractdatabaseadapter.class.php';

    /**
     * 
     */
    abstract class Database
    {
        private static $instances = array();

        private static function validateParsedPattern(&$pattern)
        {
            return isset($pattern['scheme'], $pattern['user'], $pattern['host'], $pattern['path']);
        }
        
        public static function &factory($pattern)
        {
            if (isset(self::$instances[$pattern]))
            {
                return self::$instances[$pattern];
            }
            $parsed_pattern = @parse_url($pattern);
            if ($parsed_pattern ===  false || self::validateParsedPattern($parsed_pattern) == false)
            {
                throw new DatabaseException('database connection pattern is invalid!', 401);
            }
            
            $adapter_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'adapters' . DIRECTORY_SEPARATOR . $parsed_pattern['scheme'];
            if (!file_exists($adapter_path))
            {
                throw new DatabaseException('database adapter not found!', 402);
            }

            require_once $adapter_path;
            $adapter =& $parsed_pattern['scheme'];

            $data = array(
                'host'      => (isset($parsed_pattern['']) ? $parsed_pattern[''] : null),
                'user'      => (isset($parsed_pattern['']) ? $parsed_pattern[''] : null),
                'pass'      => (isset($parsed_pattern['']) ? $parsed_pattern[''] : mull),
                'database'  => (isset($parsed_pattern['']) ? $parsed_pattern[''] : null),
                'host'      => (isset($parsed_pattern['']) ? $parsed_pattern[''] : null)
            );

            if (in_array('DatabaseAbstractAdapter', class_parents($adapter)) !== true)
            {
                throw new DatabaseException('the given database adapter is invalid', 403);
            }

            $instance = $adapter::factory();
            self::$instances[$pattern] = $instance;
            
            return self::$instances[$pattern];
        }
    }
?>
