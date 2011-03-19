<?php
    class HttpHeader implements Serializable
    {
        /**
         * Holds the name of the header
         *
         * @access public
         * @var string the name of the header
         */
        public $name;

        /**
         * Holds the name of the header
         *
         * @access public
         * @var string the value of the header
         */
        public $value;

        /**
         * Creates a HttpHeader object wth the given name and value
         *
         * @param string $name the name
         * @param string $value the value
         */
        public function __construct($name, $value)
        {
            $this->name = $name;
            $this->value = $value;
        }

        /**
         * Returns the ready header when the object is in string context
         *
         * @access public
         * @return string the header
         */
        public function __toString()
        {
            return $this->name . ': ' . $this->value;
        }

        public function serialize()
        {
            return serialize($this->name, $this->value);
        }

        public function unserialize($serialized)
        {
            $data = unserialize($serialized);
            $this->name = $data[0];
            $this->value = $data[1];
        }
    }
?>
