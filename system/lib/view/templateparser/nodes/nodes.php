<?php
    /**
     *
     */
    class Node implements ArrayAccess, IteratorAggregate, Countable, Serializable
    {
        public $name;
        public $index;
        public $data;
        public $level;
        public $children;
        public $parent;
        public $content;

        /**
         *
         */
        public function __construct($name, $index, $level, &$parent = null)
        {
            $this->name = $name;
            $this->index = $index;
            $this->data = null;
            $this->level = $level;
            $this->children = new NodeList();
            $this->parent =& $parent;
        }
        
        public function offsetGet($offset)
        {
            return $this->children->offsetGet($offset);
        }
        public function offsetSet($offset, $value)
        {
           $this->children->offsetSet($value);
        }
        public function offsetUnset($offset)
        {
            $this->children->offsetUnset($offset);
        }
        public function offsetExists($offset)
        {
            return $this->children->offsetExists($offset);
        }

        public function count()
        {
            return $this->children->count();
        }

        public function getIterator()
        {
            return $this->children->getIterator();
        }

        /**
         *
         */
        public function serialize()
        {
            serialize(array($this->name, $this->index, $this->data, $this->level, $this->children));
        }

        /**
         *
         */
        public function unserialize($serialized)
        {
            $data = unserialize($serialized);
            $this->name = $data[0];
            $this->index = $data[1];
            $this->data = $data[2];
            $this->level = $data[3];
            $this->children = $data[4];
            $this->parent = null;
        }


    }

    /**
     *
     */
    class RootNode extends Node
    {
        /**
         *
         */
        public function  __construct()
        {
            parent::__construct('__root', null, 0);
        }
    }

    /**
     *
     */
    class TextNode extends Node
    {
        /**
         *
         */
        public function  __construct($level, &$parent)
        {
            parent::__construct('__text', null, $level, $parent);
        }
    }

    /**
     *
     */
    class SingleNode extends Node
    {
        /**
         *
         */
        public function  __construct($name, $index, $data, $level, &$parent)
        {
            parent::__construct($name, $index, $level, $parent);
            $this->data = $data;
        }
    }
?>
