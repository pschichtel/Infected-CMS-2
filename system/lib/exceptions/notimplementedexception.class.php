<?php
    /**
     * 
     */
    class NotImplementedException extends Exception
    {
        /**
         * initiates the NotImplementedException object and its parent (Exception)
         *
         * @access public
         * @param string $message the exception message
         * @param int $code the exception code
         */
        public function __construct($message, $code = 0)
        {
            parent::__construct($message, $code);
        }
    }
?>
