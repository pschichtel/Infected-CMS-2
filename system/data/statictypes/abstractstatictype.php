<?php

    /**
     *
     */
    abstract class AbstractStaticType
    {
        protected $value;
        protected static $type;

        /**
         * Children have to implement their own constructer
         *
         * @access public
         * @param mixed $value the value to set
         * @param bool $convert whether to convert the given value on missmatch instead of throwing an exception
         */
        abstract public function __construct($value, $convert = false);

        /**
         * Children have to implement a method to convert the value
         *
         * @access public
         * @param &mixed $value the value to convert
         */
        abstract protected function convert(&$value);

        protected function validate(&$value)
        {
            return (gettype($value) === self::$type);
        }
        protected function setValue(&$value, $convert)
        {
            if ($convert)
            {
                 $this->convert($value);
            }
            elseif (!$this->validate($value))
            {
                throw new Exception('Given value did not match the static type.');
            }
            $this->value = $value;
        }
        public function value()
        {
            return $this->value;
        }
        public function __get($name)
        {
            return $this->value;
        }
    }

?>
