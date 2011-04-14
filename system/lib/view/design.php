<?php
    Loader::addSysDirectoryToMap('lib/View/Filters');

    class Design extends Template
    {
        protected $title;
        protected static $minorTitles = array();
    
        public function __construct($title)
        {
            $this->title = $title;
            parent::__construct('index/index');
            //$this->addPostFilter(new WhitespaceFilter());
            $this->addSubtemplate('header', new Template('index/header'));
            $this->addSubtemplate('footer', new Template('index/footer'));
        }
        
        public static function addMinorTitle($title)
        {
            self::$minorTitles[] = $title;
        }
        
        public static function clearMinorTitles()
        {
            self::$minorTitles = array();
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
    }
?>
