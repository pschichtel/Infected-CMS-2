<?php
    /**
     * Dependencies
     */
    require_once ICMS_SYS_PATH . 'lib/models/statictypes/abstractstatictype.php';

    class StaticDouble extends AbstractStaticType
    {
        protected static $type = 'double';
        public function  __construct($value, $convert = false)
        {
            self::$type = 'double';
            $this->setValue($value, $convert);
        }
        protected function convert(&$value)
        {
            $value = doubleval($value);
        }
    }
?>
