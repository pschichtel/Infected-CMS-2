<?php
    /**
     * Dependencies
     */
    require_once dirname(__FILE__) . '/abstractstatictype.php';

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
