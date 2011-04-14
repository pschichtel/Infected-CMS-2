<?php
    /**
     * 
     */
    interface IResponse
    {
        public function setContent($content);
        public function getContent();
        public function send();
    }
?>
