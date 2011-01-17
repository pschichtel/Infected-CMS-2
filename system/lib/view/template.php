<?php
    /**
     * 
     */
    class Template
    {
        protected static $tplPaths = array();
        protected static $instructionSet = array(
            'ViewHelper' => 'instruction_ViewHelper',
            'Model' => 'instruction_Model',
            'SubTemplate' => 'instruction_SubTemplate',
            'Widget' => 'instruction_Widget',
            'Lang' => 'instruction_Lang'
        );

        protected $tplPath;
        protected $lang;
        protected $models;
        protected $subTemplates;
        protected $viewHelper;
        
        public function __construct($template)
        {
            $this->tplPath = $this->findTemplate($template);
            $this->lang = null;
            $this->models = array();
            $this->subTemplates = array();
            $this->viewHelper = array();
        }

        public static function addTemplatePath($path)
        {
            $path = rtrim($path, '/\\');
            if (!in_array($path, self::$tplPaths))
            {
                self::$tplPaths[] = $path;
                return true;
            }
            return false;
        }

        public static function getTemplatePaths($reversed = true)
        {
            if ($reversed)
            {
                return array_reverse(self::$tplPaths);
            }
            else
            {
                return self::$tplPaths;
            }
        }

        protected function findTemplate($template)
        {
            $template = ltrim($template, '/\\');

            if (Registry::exists('template_path'))
            {
                $tmp = Registry::get('template_path') . Design::name() . '/' . $template;
                if (file_exists($tmp))
                {
                    return $tmp;
                }
            }

            $tplPaths = array_reverse(self::$tplPaths);
            foreach ($tplPaths as $tplPath)
            {
                $tmp = $tplPath . '/' . $template;
                if (file_exists($tmp))
                {
                    return $tmp;
                }
            }
        }
        
        protected function load()
        {
            return file_get_contents($this->tplPath);
        }

        protected function parse($tpl)
        {
            $instructions = implode('|', array_keys(self::$instructionSet));
            $regex = "/\{($instructions)\<([\w\d]+?)\>(\:(.+?))?\}/m";
            echo htmlspecialchars($regex) . "\n\n\n";
            var_dump(preg_match_all($regex, $tpl, $matchesarray, PREG_OFFSET_CAPTURE));
            $matchesarray = $matchesarray[0];
            foreach ($matchesarray as $index => $match)
            {
                echo htmlspecialchars($match[0]);
            }

            echo "\n\n\n\n";

        }

        public function render()
        {
            $tpl = $this->load();
            echo htmlspecialchars($tpl) . "\n\n\n";
            $this->parse($tpl);
        }

        public function setLang(Lang $lang)
        {
            $this->lang = $lang;
        }

        public function addData($name, array $data)
        {
            if (!isset($this->models[$name]))
            {
                $this->models[$name] = $data;
            }
            return $this;
        }

        public function removeData($name)
        {
            if (isset($this->models[$name]))
            {
                unset($this->models[$name]);
            }
            return $this;
        }

        public function addSubTemplate($name, Template $subtpl)
        {
            if (!isset($this->subTemplates[$name]))
            {
                $this->subTemplates[$name] = $subtpl;
            }
            return $this;
        }

        public function removeSubTemplate($name)
        {
            if (isset($this->subTemplates[$name]))
            {
                unset($this->subTemplates[$name]);
            }
            return $this;
        }

        public function addViewHelper($name, AbstractViewHelper $viewhelper)
        {
            if (!isset($this->viewHelper[$name]))
            {
                $this->viewHelper[$name] = $viewhelper;
            }
            return $this;
        }

        public function removeViewHelper($name)
        {
            if (isset($this->viewHelper[$name]))
            {
                unset($this->viewHelper[$name]);
            }
            return $this;
        }

        protected function instruction_ViewHelper()
        {

        }

        protected function instruction_Model()
        {

        }

        protected function instruction_SubTemplate()
        {

        }

        protected function instruction_Widget()
        {

        }

        protected function instruction_Lang()
        {

        }
    }
?>
