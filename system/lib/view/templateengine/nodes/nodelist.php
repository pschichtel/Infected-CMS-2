<?php
    /**
     *
     */
    class NodeList implements ArrayAccess, IteratorAggregate, Countable, Serializable
    {
        protected $list;
        protected $length;
        public $last;

        public function __construct()
        {
            $this->list = array();
            $this->length = 0;
            $this->last = null;
        }

        public function addNode(Node $node)
        {
            $this->list[] = $node;
            $this->last =& $this->list[$this->length];
            $this->length++;
        }
        
        public function offsetGet($offset)
        {
            return (isset($this->list[$offset]) ? $this->list[$offset] : null);
        }
        public function offsetSet($offset, $value)
        {
            if ($value instanceof Node)
            {
                $this->addNode($value);
            }
        }
        public function offsetUnset($offset)
        {
            unset($this->list[$offset]);
        }
        public function offsetExists($offset)
        {
            return isset($this->list[$offset]);
        }

        public function count()
        {
            return $this->length;
        }

        public function getIterator()
        {
            return new ArrayIterator($this->list);
        }

        public function serialize()
        {
            return serialize($this->list);
        }
        public function unserialize($serialized)
        {
            $this->list = unserialize($serialized);
        }
    }
?>
