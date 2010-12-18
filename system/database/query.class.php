<?php
    /**
     *
     */
    class Query
    {
        protected $query;
        protected $typeConfig;
        protected $tablePref;

        protected $baseCommand;
        protected $baseAdditionCommand;
        protected $additionAdditionCommand;
        protected $conditionCommand;
        protected $orderCommand;
        protected $limitCommand;

        private function __construct($queryTypeConfig, $tablePref)
        {
            $this->query = '';
            $this->typeConfig = $queryTypeConfig;
            $this->tablePref = $tablePref;
        }

        public function __destruct()
        {

        }

        public static function factory($queryType, $tablePref = '')
        {
            $filepath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'querytypes' . DIRECTORY_SEPARATOR . $queryType . '.ini';
            if (is_string($queryType) && file_exists($filepath))
            {
                return new self(parse_ini_file($filename, true), $tablePref);
            }
        }

        public static function condition($condStr)
        {
            
        }

        protected function is_function($col)
        {
            if (strpos($col, '(') === false)
            {
                return false;
            }
            return true;
        }

        protected function quote_name($col)
        {
            if ($this->is_function($col))
            {
                return $col;
            }
            return $this->typeConfig['quotes']['names'] . $col . $this->typeConfig['quotes']['names'];
        }

        protected function buildColString($cols)
        {
            $finalCols = array();
            $quote =& $this->typeConfig['quotes']['names'];
            foreach ($cols as &$col)
            {
                if (is_array($col))
                {
                    $finalCols[] = $this->quote_name($col[0]) . ' AS ' . $quote . $col[1] . $quote;
                }
                else
                {
                    $finalCols[] = $quote . $col . $quote;
                }
            }
            return implode(',', $finalCols);
        }
        
        protected function escape_string($string)
        {
            $quote =& $this->typeConfig['quotes']['strings'];
            $escaper =& $this->typeConfig['escaping']['generell'];
            return str_replace($quote, $escaper . $quote, $string);
        }

        protected function buildValueString($values)
        {
            $finalValues = array();
            $quote =& $this->typeConfig['quotes']['strings'];
            foreach ($values as &$value)
            {
                switch (gettype($value))
                {
                    case 'string':
                        $finalValues[] = $quote . $this->escape_string($value) . $quote;
                        break;
                    case 'integer':
                        $finalValues[] = intval($value);
                        break;
                    case 'double':
                        $finalValues[] = doubleval($value);
                        break;
                    case 'NULL':
                        $finalValues[] = 'NULL';
                        break;
                    default:
                        $finalValues[] = (string) $value;

                }
            }
        }

        protected function buildTablename($name)
        {
            $quote =& $this->typeConfig['quotes']['names'];
            if (is_array($name))
            {
                $name = $name[0] . '.' . $this->tablePref . $name[1];
            }
            else
            {
                $name = $this->tablePref . $name;
            }
            return $quote . $name . $quote;
        }

        public function reset()
        {
            $this->query = '';
            $this->baseAdditionCommand = true;
            $this->baseAdditionCommand = false;
            $this->additionAdditionCommand = false;
            $this->conditionCommand = false;
            $this->orderCommand = false;
            $this->limitCommand = false;
        }

        public function getQuery()
        {
            $query = $this->query;
            $this->reset();
            return $query;
        }

        public function select()
        {
            if (!$this->baseCommand)
            {
                throw new DatabaseException('[Query::select()] Wrong command order!');
            }
            $args = func_get_args();

            $quote =& $this->typeConfig['quotes']['names'];
            $this->query .= 'SELECT ' . $quote;
            $this->query .= implode($quote . ',' . $quote, $args);
            $this->query .= $quote . ' ';
            
            $this->baseCommand = false;
            $this->baseAdditionCommand = true;

            return $this;
        }

        public function from()
        {
            if (!$this->baseAdditionCommand)
            {
                throw new DatabaseException('[Query::from()] Wrong command order!');
            }
            $args = func_get_args();
            
            $quote =& $this->typeConfig['quotes']['names'];
            $this->query .= $args;
            $this->query .= $this->buildColString($args);
            $this->query .= ' ';

            $this->baseAdditionCommand = false;
            $this->additionAdditionCommand = true;

            return $this;
        }

        public function insert_into($table)
        {
            if (!$this->baseCommand)
            {
                throw new DatabaseException('[Query::insert_into()] Wrong command order!');
            }

            $this->query .= 'INSERT INTO ';
            $this->query .= $this->buildTablename($table);
            $this->query .= ' ';

            //$colStr
        }

        public function values($values)
        {
            if (!$this->baseAdditionCommand)
            {
                throw new DatabaseException('[Query::values()] Wrong command order!');
            }

            $cols = array_keys($values);
            $values = array_values($values);
            unset($values);
        }

    }

    class QueryCondition
    {
        const TOK_AND = 0x01;
        const TOK_OR = 0x02;
        const TOK_LIKE = 0x03;

        protected $tree;
        protected $level;
        protected $current;
        protected $parent;
        protected $connectorNeeded;

        protected $counter;


        public function __construct()
        {
            $this->tree[0] = array();
            $this->level = 0;
            $this->current =& $this->tree[$level];
            $this->parent =& $this->tree[0];
            $this->connecterNeeded = false;

            $this->counter = 0;
        }

        public function add($condition, $connector = self::TOK_AND)
        {
            if ($this->connectorNeeded)
            {
                $this->current['conn_' . $counter] = $connector;
            }
            $this->current['cond_' . $this->counter] = $condition;
        }

        public function open_bracket()
        {
            $this->parent =& $this->current;
            $this->tree[$this->level][$this->level + 1] = array();
            $this->current =& $this->tree[$this->level][$this->level + 1];
            $this->level++;

        }

        public function close_bracket()
        {

        }
    }
?>
