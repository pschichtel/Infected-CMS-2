<?php
    /**
     * 
     */
    class Stack
    {
        protected $stack;
        protected $size;

        public function __construct(array $stack = null)
        {
            if ($stack === null)
            {
                $this->stack = array();
                $this->size = 0;
            }
            else
            {
                $this->stack = array_values($stack);
                $this->size = count($this->stack);
            }
        }

        public function push($value)
        {
            array_push($this->stack, $value);
            $this->size++;
            return $this;
        }

        public function pop()
        {
            $this->size--;
            return array_pop($this->stack);
        }

        public function top()
        {
            if (isset($this->stack[$this->size - 1]))
            {
                return $this->stack[$this->size - 1];
            }
            else
            {
                return null;
            }
        }

        public function size()
        {
            return $this->size;
        }
    }
?>
