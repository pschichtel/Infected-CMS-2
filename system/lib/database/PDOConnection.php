<?php
    class PDOConnection extends PDO
    {
        public function __construct(IConfig $config)
        {
            if (!$config->exists('driver'))
            {
                throw new DatabaseException('There was now datebase driver specified!');
            }
            $driver = $config->get('driver');
            if (!self::driverExists($driver))
            {
                throw new DatabaseException('The specified driver does not exist!');
            }
            
            $dsn = $driver . ':';
            
            if ($config->exists('host'))
            {
                $dsn .= 'host=' . $config->get('host') . ';';
            }
            
            if ($config->exists('dbname'))
            {
                $dsn .= 'dbname=' . $config->get('dbname') . ';';
            }
            
            if ($config->exists('file'))
            {
                $dsn .= 'file=' . $config->get('file') . ';';
            }
            
            $dsn = rtrim($dsn, ';');
            
            $user = $config->exists('user');
            $pass = $config->exists('pass');
            $options = $config->exists('options');
            
            try
            {
                if ($user && $pass && $options)
                {
                    parent::__construct($dsn, $config->get('user'), $config->get('pass'), $config->get('options'));
                }
                elseif ($user && $pass)
                {
                    parent::__construct($dsn, $config->get('user'), $config->get('pass'));
                }
                elseif ($user)
                {
                    parent::__construct($dsn, $config->get('user'));
                }
                else
                {
                    parent::__construct($dsn);
                }
            }
            catch(PDOException $e)
            {
                throw new DatabaseException('Failed to connect to the database! Error: ' . $e->getMessage());
            }
            catch(Exception $e)
            {
                throw new DatabaseException('An unknown error occured! Message: ' . $e->getMessage());
            }
        }
        
        public static function driverExists($driver)
        {
            return in_array($driver, parent::getAvailableDrivers());
        }
    }
?>
