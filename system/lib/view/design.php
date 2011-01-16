<?php
    /**
     * Dependencies
     */

    /**
     * 
     */
    class Design extends Template
    {
        private static $designName = '';

        private $activeDesign;

        public function __construct()
        {
            $this->session = Session::instance();
            $this->config = Config::instance();
            $this->request = Request::instance();
            
            $this->activeDesign = $this->getActiveDesign();
            self::$designName = $this->activeDesign;
            $this->config = new INIConfigFile(Registry::get('design_path') . $this->activeDesign . '/design.ini');

            parent::__construct('index.tpl');
        }
        
        public function  __destruct()
        {}

        private function getActiveDesign()
        {
            if ($this->request->exists('get', 'design'))
            {
                return $this->request->get('get', 'design');
            }
            elseif ($this->session->exists('design'))
            {
                return $this->session->get('design');
            }
            else
            {
                return $this->config->get('core', 'design');
            }
        }

        public static function name()
        {
            return self::$designName;
        }

    }
?>
