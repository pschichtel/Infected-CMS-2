<?php
    /**
     * Dependencies
     */
    Application::import('data::statictypes::abstractstatictype');

    class StaticArray extends AbstractStaticType implements ArrayAccess, IteratorAggregate, Countable, Serializable
    {
        public function  __construct($value, $convert = false)
        {
            self::$type = 'array';
            $this->setValue($value, $convert);
        }
        public function __destruct()
        {
            $this->destruct();
        }
        protected function destruct()
        {
            unset($this->value);
        }
        protected function convert(&$value)
        {
            $value = array($value);
        }
        
        public function offsetGet($offset)
        {
            return (isset($this->value[$offset]) ? $this->value[$offset] : null);
        }
        public function offsetSet($offset, $value)
        {
           $this->value[$offset] = $value;
        }
        public function offsetUnset($offset)
        {
            unset($this->value[$offset]);
        }
        public function offsetExists($offset)
        {
            return isset($this->value[$offset]);
        }

        public function count()
        {
            return count($this->value);
        }

        public function getIterator()
        {
            return new ArrayIterator($this->value);
        }

        public function serialize()
        {
            $serialized = serialize($this->value);
            $this->destruct();
            return $serialized;
        }
        public function unserialize($serialized)
        {
            $this->setValue(unserialize($serialized));
        }
    }
?>
