<?php
    /**
     *
     */
    class INIConfigFile implements IConfigFile
    {
        public static $configs = array();
        protected $filepath;
        protected $activConfig;
        protected static $reservedKeywords = array('null', 'yes', 'no', 'true', 'false', 'on', 'off', 'none');
        
        public function __construct($filepath, $suffix = '.php')
        {
            $filepath .= $suffix;
            $this->filepath = $filepath;
            $this->activConfig = $this->load();
        }

        public function load($reload = false)
        {
            if (!isset(self::$configs[$this->filepath]) || $reload)
            {
                if (!is_readable($this->filepath))
                {
                    return array();
                }

                $tmp = parse_ini_file($this->filepath);
                if ($tmp === false || !is_array($tmp))
                {
                    throw new ConfigException('A invalid config file was given or the given private key was invalid', 402);
                }
                return $tmp;
            }
            else
            {
                return self::$configs[$this->filepath];
            }
        }

        protected function array2ini($name, array $array)
        {
            $name = strval($name);
            $tmp = '';
            foreach ($array as $index => $value)
            {
                if (!is_array($value) && !is_object($value))
                {
                    $index = strval($index);
                    $value = strval($value);
                    $tmp .= "{$name}[{$index}]={$value}\n";
                }
            }
            return $tmp;
        }

        public function save()
        {
            if (!is_writable($this->filepath))
            {
                throw new ConfigException('The config file is not writable!', 403);
            }

            $tmp = ";<?php __halt_compiler() ?>\n";
            foreach ($this->activConfig as $index => $value)
            {
                if (is_object($value))
                {
                    continue;
                }
                elseif (is_array($value))
                {
                    $tmp .= $this->array2ini($name, $value);
                }
                else
                {
                    $tmp .= strval($index) . '=' . strval($value) . "\n";
                }
            }
            file_put_contents($this->filepath, $tmp);
        }

        public function get($name, $default = null)
        {
            if ($this->exists($name))
            {
                return $this->activConfig[$name];
            }
            else
            {
                return $default;
            }
        }

        public function set($name, $value, $dontoverwrite = false)
        {
            if (in_array($name, self::$reservedKeywords))
            {
                return false;
            }
            if (!$dontoverwrite)
            {
                $this->activConfig[$name] = $value;
                return true;
            }
            else
            {
                if (!$this->exists($name))
                {
                    $this->activConfig[$name] = $value;
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }

        public function setMultiple(array $data)
        {
            foreach ($data as $name => $value)
            {
                $this->activConfig[$name] = $value;
            }
        }

        public function setConfig(array $config)
        {
            $this->activConfig = $config;
        }

        public function getAll()
        {
            return $this->activConfig;
        }

        public function exists($name)
        {
            return isset($this->activConfig[$name]);
        }
    }
?>
