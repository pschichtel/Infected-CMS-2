<?php

    /**
     * Factory class to get a specified database adapter
     */
    abstract class Database
    {
        private static $adapterInstances = array();
        
        public static function &factory($pattern)
        {
            if (isset(self::$adapterInstances[$pattern]))
            {
                return self::$adapterInstances[$pattern];
            }
            $parsed_pattern = @parse_url($pattern);

            if ($parsed_pattern ===  false)
            {
                throw new DatabaseException('Database::factory(1): The database connection pattern is invalid!', 401);
            }
            if (!isset($parsed_pattern['scheme']))
            {
                throw new DatabaseException('Database::factory(1): No database adapter given!', 402);
            }
            $adapter = ucfirst(strtolower($parsed_pattern['scheme']));
            
            try
            {
                Loader::addSysDirectoryToMap('lib/Database/Adapters/' . $adapter);
            }
            catch (Exception $e)
            {
                throw new DatabaseException('Database::factory(1): Failed to add the adapter path to the loader!', 407);
            }
            
            $adapter .= 'Adapter';
            
            if (!Loader::load($adapter))
            {
                throw new DatabaseException('Database::factory(1): database adapter ' . $adapter . ' could not be loaded!', 404);
            }

            if (!class_exists($adapter))
            {
                throw new DatabaseException('Database::factory(1): the given database adapter is invalid', 403);
            }

            if (in_array('IDatabaseAdapter', class_implements($adapter)) !== true)
            {
                throw new DatabaseException('Database::factory(1): The given database adapter is invalid', 405);
            }

            if (!$adapter::validate($parsed_pattern))
            {
                throw new DatabaseException('Database::factory(1): The pattern validation failed for the fiven adapter!', 406);
            }
            $instance = new $adapter($parsed_pattern);
            self::$adapterInstances[$pattern] = $instance;
            
            return self::$adapterInstances[$pattern];
        }

        public static function factorFromConfig(IConfigFile $config)
        {
            $scheme = false;
            $user = false;
            $pass = false;
            $host = false;

            $uri = '';
            if ($config->exists('adapter'))
            {
                $uri .= $config->get('adapter') . '://';
                $scheme = true;
            }
            if ($config->exists('user'))
            {
                $uri .= $config->get('user');
                $user = true;
            }
            if ($config->exists('pass'))
            {
                $pass = $config->get('pass');
                if ($pass !== '')
                {
                    $uri .= ($user ? ':' : '') . $pass;
                    $pass = true;
                }
            }
            if ($config->exists('host'))
            {
                if ($user || $pass)
                {
                    $uri .= '@';
                }
                $uri .= $config->get('host');
                $host = true;
            }
            if ($config->exists('database'))
            {
                $uri .= ($host ? '/' : '') . $config->get('database');
            }
            if ($config->exists('prefix'))
            {
                $uri .= '?' . $config->get('prefix');
            }
            if ($config->exists('charset'))
            {
                $uri .= '#' . $config->get('charset');
            }
            
            return self::factory($uri);
        }
    }
?>
