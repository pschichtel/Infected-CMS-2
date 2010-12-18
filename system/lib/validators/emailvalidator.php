<?php

    /**
     *
     */
    class EmailValidator implements IValidator
    {
        public function isValid($value)
        {
            return (bool) preg_match("/[(\w\d\-\.]{3,}@([a-z\d-]{2,}\.)+[a-z\d]{2,4}/Us", mb_strtolower($string));
        }
    }

?>