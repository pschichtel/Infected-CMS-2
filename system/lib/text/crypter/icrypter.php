<?php
    /**
     *
     */
    interface ICrypter
    {
        public function __construct($key, $algo);
        public function encrypt($data);
        public function decrypt($data);
    }
?>
