<?php

    /**
     *
     */
    abstract class AbstractFrontController
    {
        private $name;
        public function getName()
        {
            return $name;
        }
        
        abstract function run();
    }

?>
