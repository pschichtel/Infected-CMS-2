<?php
    /**
     *
     */
    require_once ICMS_SYS_PATH . 'lib/models/stack.php';
    
    /**
     *
     */
    class Template
    {
        protected static $tplPaths = array();
        protected static $tags = array(
            'Model' => 'instruction_Model'
        );
        protected static $singleTags = array(
            'ViewHelper' => 'instruction_ViewHelper',
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
            $tags = array();
            
            $openTags = implode('|', array_keys(self::$tags));
            $singleTags = implode('|', array_keys(self::$singleTags));

            //$regex = "/\{(?:(?P<stag>ViewHelper)<(?P<sindex>[\w\d]+)>(?::(?P<action>[\w\d]+))?)|(?:(?P<tag>Model)<(?P<index>[\w\d]+)>)|(?:\/(?P<ctag>Model))\}/s";
            $this->getTagsByRegex($tags, $tpl, 'single', "/\{($singleTags)<([\w\d]+)>(?::([^\}]+))?\}/");

            $this->getTagsByRegex($tags, $tpl, 'open', "/\{($openTags)<([\w\d]+)>\}/");

            $this->getTagsByRegex($tags, $tpl, 'close', "/\{(?:\/($openTags))\}/");
            
            ksort($tags, SORT_NUMERIC);
            //$tags = array_values($tags);
            var_dump($tags);

            $compiledTemplate = array();
            $lastPos = 0;
            $tplLength = strlen($tpl);
            $tagsCount = count($tags);
            $tagStack = new Stack();

            echo "$tplLength\n";

            $counter = 1;

            foreach ($tags as $index => &$tag)
            {
                $text = trim(substr($tpl, $lastPos, $tag['pos'] - $lastPos));
                if (!empty($text))
                {
                    $text = preg_replace('/>\s+</', '><', $text);
                    $compiledTemplate[] = $text;
                }
                if ($tag['type'] == 'single')
                {
                    $compiledTemplate[] = $tags[$index];
                }
                elseif ($tag['type'] == 'open')
                {
                    $compiledTemplate[] = $tags[$index];
                    $tagStack->push($tag['name']);
                }
                elseif ($tag['type'] == 'close')
                {
                    if ($tagStack->top() == $tag['name'])
                    {
                        $compiledTemplate[] = $tags[$index];
                        $tagStack->pop();
                    }
                }
                $lastPos = $tag['pos'] + $tag['length'];
                $counter++;
            }
            $text = trim(substr($tpl, $lastPos));
            if (!empty($text))
            {
                $text = preg_replace('/>\s+</', '><', $text);
                $compiledTemplate[] = $text;
            }

            echo "\n\n\n\n";

            var_dump($compiledTemplate);

    printRuntime('Template-Engine');

            echo "\n\n\n\n";

        }

        protected function getTagsByRegex(array& $array, &$tpl, $type, $regex)
        {
            preg_match_all($regex, $tpl, $matchesarray, PREG_OFFSET_CAPTURE);

            foreach ($matchesarray[0] as $index => $match)
            {
                $array[$match[1]] = array(
                    'type'      => $type,
                    'pos'       => $match[1],
                    'length'    => strlen($match[0]),
                    'name'      => $matchesarray[1][$index][0],
                    'index'     => (empty($matchesarray[2][$index][0]) ? null : $matchesarray[2][$index][0]),
                    'data'      => (empty($matchesarray[3][$index][0]) ? null : $matchesarray[3][$index][0])
                );
            }
        }

        public function render()
        {
            echo " __ __ CALLED __ __\n\n";
            //$tpl = $this->load();
            //echo htmlspecialchars($tpl) . "\n\n\n";
            //$this->parse($tpl);
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
