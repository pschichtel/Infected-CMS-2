<?php
    /**
     * Dependencies
     */
    Application::import('data::statictypes::abstractstatictype');

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
