<?php
    Loader::addSysDirectoryToMap('lib/View/Filters');

    class Design extends Template
    {
        protected $title;
        protected static $minorTitles = array();
        
        public static function name()
        {
            return 'TEST';
        }
    
        public function __construct()
        {
            $this->title = '';
            parent::__construct('index');
            //$this->addPostFilter(new WhitespaceFilter());
            //$this->addSubtemplate('header', new Template('index/header'));
            //$this->addSubtemplate('footer', new Template('index/footer'));
        }
        
        public static function addMinorTitle($title)
        {
            self::$minorTitles[] = $title;
        }
        
        public static function clearMinorTitles()
        {
            self::$minorTitles = array();
        }
        
        public function setTitle($title)
        {
            $this->title = strval($title);
        }
        
        public function getTitle()
        {
            return $this->title;
        }
        
        public function render()
        {
            $this->addVar('title', $this->title);
            $this->addVar('minorTitles', self::$minorTitles);
            return parent::render();
        }
        
        public function setContentTpl(Template $tpl)
        {
            $this->addSubtemplate('content', $tpl);
        }
        
        protected function subTemplate($tpl)
        {
            $this->displaySubtemplate($tpl);
        }
    }
?>
