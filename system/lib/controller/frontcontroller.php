<?php
    /**
     * Dependencies
     */
    require_once ICMS_SYS_PATH . 'lib/database/database.php';
    require_once ICMS_SYS_PATH . 'lib/configuration/config.php';
    require_once ICMS_SYS_PATH . 'lib/text/crypter/aescrypter.php';
    //require_once ICMS_SYS_PATH . 'lib/';

    /**
     *
     */
    final class Frontcontroller
    {
        public function __construct()
        {
            
        }

        public function __destruct()
        {
            
        }

        public function run()
        {
            var_dump(Registry::getAll());
            $crypter = new AESCrypter('supersicher', 2);
            echo "\n\n\n";
            $data = 'test ';
            $encrypted = $crypter->encrypt($data);
            $decrypted = $crypter->decrypt($encrypted);
            echo "raw:       '$data'\n";
            echo "encrypted: '$encrypted'\n";
            echo "decrypted: '$decrypted'\n";
        }
    }
?>
