<?php
    /**
     * Dependencies
     */
    require_once ICMS_SYS_PATH . 'lib/models/statictypes/abstractstatictype.php';

    class StaticInteger extends AbstractStaticType
    {
        public function  __construct($value, $convert = false)
        {
            self::$type = 'integer';
            $this->setValue($value, $convert);
        }
        protected function convert(&$value)
        {
            $value = intval($value);
        }
    }
?>
