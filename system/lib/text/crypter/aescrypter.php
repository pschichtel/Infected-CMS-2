<?php
    /**
     * Dependencies
     */
    require_once ICMS_SYS_PATH . 'lib/text/crypter/icrypter.php';
    
    /**
     *
     */
    class AESCrypter implements ICrypter
    {
        protected $key;
        protected $algo;
        private static $algos = array(MCRYPT_RIJNDAEL_128, MCRYPT_RIJNDAEL_192, MCRYPT_RIJNDAEL_256);

        public function  __construct($key, $algo = 0)
        {
            if (!isset(self::$algos[$algo]))
            {
                throw new Exception('AESCrypter::__construct: algorithem ID not available!', 404);
            }
            $this->algo = &self::$algos[$algo];
            $this->key = substr($key, 0, mcrypt_get_key_size($this->algo, MCRYPT_MODE_ECB));
        }

        public function encrypt($data)
        {
            $iv_size = mcrypt_get_iv_size($this->algo, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $crypted = mcrypt_encrypt($this->algo, $this->key, $data, MCRYPT_MODE_ECB, $iv);
            return base64_encode($crypted);
        }

        public function decrypt($data)
        {
            $crypted = base64_decode($data);
            $iv_size = mcrypt_get_iv_size($this->algo, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            return rtrim(mcrypt_decrypt($this->algo, $this->key, $crypted, MCRYPT_MODE_ECB, $iv), "\0");
        }
    }
?>
