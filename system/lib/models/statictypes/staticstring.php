<?php
    /**
     * Dependencies
     */
    require_once ICMS_SYS_PATH . 'lib/models/statictypes/abstractstatictype.php';

    class StaticString extends AbstractStaticType
    {
        public function  __construct($value, $convert = false)
        {
            self::$type = 'string';
            $this->setValue($value, $convert);
        }
        protected function convert(&$value)
        {
            $value = (string) $value;
        }
    }
?>
