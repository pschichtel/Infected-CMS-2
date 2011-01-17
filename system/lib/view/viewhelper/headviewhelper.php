<?php
    /**
     *
     */
    class HeadViewHelper extends AbstractViewHelper
    {
        private $config;

        public function __construct(IConfigFile& $config)
        {
            $thids->config =& $config;
        }

        public function action_css()
        {
            return "";
        }

        public function action_meta()
        {
            return "";
        }

        public function action_style()
        {
            return "";
        }

        public function action_title()
        {
            return "";
        }

        public function action_dtd()
        {
            return "";
        }
    }
?>