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

        public function __construct($filepath, $privatekey)
        {
            $this->filepath = $filepath;
            list($this->activConfig, $this->crypter) = $this->load($filepath, $privatekey);
        }

        public function load($filepath, $privatekey)
        {
            if (!isset(self::$configs[$filepath]))
            {
                $crypter = new AESCrypter($privatekey, 1);

                if (!file_exists($filepath))
                {
                    return array(array(), $crypter);
                }

                $tmp = @file_get_contents($filepath);
                if ($tmp === false)
                {
                    throw new ConfigException('[Configuration::load] The config file exists, butcould not be loaded!', 401);
                }

                $tmp = $crypter->decrypt($tmp);

                $tmp = @unserialize($tmp);
                if ($tmp === false || !is_array($tmp))
                {
                    throw new ConfigException('[Configuration::load] A invalid config file was given or the given private key was invalid', 402);
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
                //throw new ConfigException('[Configuration::save] The config file is not writable!', 403);
            }
            $tmp = serialize($this->activConfig);
            $tmp = $this->crypter->encrypt($tmp);
            file_put_contents($this->filepath, $tmp);
        }

        public function get($name)
        {
            if ($this->exists($name))
            {
                return $this->activConfig[$name];
            }
            else
            {
                return null;
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
