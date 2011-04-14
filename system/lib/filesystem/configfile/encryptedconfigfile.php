<?php
    /**
     *
     */
    class EncryptedConfigFile implements IConfigFile
    {
        public static $configs = array();
        private $activConfig;
        private $crypter;
        private $filepath;
        protected $privatekey;

        public function __construct($filepath, $privatekey)
        {
            $this->filepath = $filepath;
            $this->privatekey = $privatekey;
            list($this->activConfig, $this->crypter) = $this->load();
        }

        public function load()
        {
            if (!isset(self::$configs[$this->filepath]))
            {
                $crypter = new AESCrypter($this->privatekey, 1);

                if (!file_exists($this->filepath))
                {
                    return array(array(), $crypter);
                }

                $tmp = @file_get_contents($filepath);
                if ($tmp === false)
                {
                    throw new ConfigException('The config file exists, butcould not be loaded!', 401);
                }

                $tmp = $crypter->decrypt($tmp);

                $tmp = @unserialize($tmp);
                if ($tmp === false || !is_array($tmp))
                {
                    throw new ConfigException('A invalid config file was given or the given private key was invalid', 402);
                }
                return array($tmp, $crypter);
            }
            else
            {
                return self::$configs[$filepath];
            }
        }

        public function save()
        {
            if (!is_writable($this->filepath))
            {
                throw new ConfigException('The config file is not writable!', 403);
            }
            $tmp = serialize($this->activConfig);
            $tmp = $this->crypter->encrypt($tmp);
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
