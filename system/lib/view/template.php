<?php

    class Template implements IView
    {
        protected static $tplPaths = array();
        
        protected $file;
        protected $vars;
        protected $subtemplates;
        protected $postFilters;
        protected $views;
    
        public function __construct($file)
        {
            $tplpath = '';
            if (is_readable($file))
            {
                $tplpath =& $file;
            }
            else
            {
                $tpl = $this->findTemplate($file);
                if ($tpl)
                {
                    $tplpath = $tpl;
                }
                else
                {
                    throw new ViewException('Template file not found or not readable!');
                }
            }
            $this->file = $tplpath;
            
            $this->vars = array();
            $this->subtemplates = array();
            $this->postFilters = array();
            $this->views = array();
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

            if (Registry::exists('template.path'))
            {
                $tmp = Registry::get('template.path') . Design::name() . '/' . $template . '.tpl.php';
                if (file_exists($tmp))
                {
                    return $tmp;
                }
            }
            
            foreach (self::getTemplatePaths() as $tplPath)
            {
                $tmp = $tplPath . '/' . $template;
                if (is_readable($tmp))
                {
                    return $tmp;
                }
            }
            
            return false;
        }
        
        public function __destruct()
        {}
        
        public function __toString()
        {
            try
            {
                return $this->render();
            }
            catch (Exception $e)
            {
                return $e->getMessage();
            }
        }
        
        public function render()
        {
            foreach ($this->vars as $name => $var)
            {
                if (preg_match('/(^\d|this)/', $name))
                {
                    $name = '_' . $name;
                }
                
                $$name = $var;
            }
            
            ob_start();
            
            include $this->file;
            
            $content = ob_get_clean();
            
            foreach ($this->postFilters as $filter)
            {
                $filter->execute($content);
            }
            
            return $content;
        }
        
        public function display()
        {
            echo $this->render();
        }
        
        public function subtemplateExists($tpl)
        {
            return isset($this->subtemplates[$tpl]);
        }
        
        protected function renderSubtemplate($tpl)
        {
            if ($this->subtemplateExists($tpl))
            {
                /**
                 * @todo clone or reference ?
                 */
                $tpl = clone $this->subtemplates[$tpl];
                //$tpl =& $this->subtemplates[$tpl];
                /*****/
                
                $tpl->addVarsAssoc($this->vars);
                return $tpl->render();
            }
            else
            {
                return '';
            }
        }
        
        protected function displaySubtemplate($tpl)
        {
            echo $this->renderSubtemplate($tpl);
        }
        
        protected function renderTemplateFile($tplpath)
        {
            try
            {
                $tpl = new Template($tplpath);
                return $tpl->render();
            }
            catch(Exception $e)
            {
                return '';
            }
        }
        
        protected function displayTemplateFile($tplpath)
        {
            echo $this->renderTemplateFile($tplPath);
        }
        
        public function addSubtemplate($name, Template $tpl)
        {
            $this->subtemplates[strval($name)] = $tpl;
            return $this;
        }
        
        public function addSubtemplates(array $names, array $tpls)
        {
            $limit = min(count($names), count($tpls));
            
            for ($i = 0; $i < $limit; $i++)
            {
                if ($tpls[$i] instanceof Template)
                {
                    $this->addSubtemplate($names[$i], $tpls[$i]);
                }
            }
            return $this;
        }
        
        public function &getSubtemplate($name)
        {
            if ($this->subtemplateExists($name))
            {
                return $this->subtemplates[$name];
            }
            else
            {
                return null;
            }
        }
        
        public function varExists($name)
        {
            return isset($this->vars[$name]);
        }
        
        public function addVar($name, $value)
        {
            $this->vars[trim(strval($name))] = $value;
            return $this;
        }
        
        public function addVars(array $names, array $values)
        {
            $limit = min(count($names), count($value));
            
            for ($i = 0; $i < $limit; $i++)
            {
                $this->addVar($names[$i], $values[$i]);
            }
        }
        
        public function addVarsAssoc(array $map)
        {
            foreach ($map as $name => $value)
            {
                $this->addVar($name, $value);
            }
            return $this;
        }
        
        public function getVar($name, $default = null)
        {
            $name = strval($name);
            if ($this->varExists($name))
            {
                return $this->vars[$name];
            }
            else
            {
                return $default;
            }
        }
        
        public function addPostFilter(IFilter $filter)
        {
            $this->postFilters[] = $filter;
            return $this;
        }
        
        public function getPostFilters()
        {
            return $this->postFilters;
        }
        
        public function clearPostFilters()
        {
            $this->postFilters = array();
            return $this;
        }

        public function viewExists($name)
        {
            return isset($this->views[$name]);
        }

        public function addView($name, IView $widget)
        {
            $this->views[strval($name)] = $widget;
            return $this;
        }

        public function addViewsAssoc(array $widgets)
        {
            foreach ($widgets as $name => $widget)
            {
                if ($widget instanceof IWidget)
                {
                    $this->views[strval($name)] = $widget;
                }
            }
            return $this;
        }

        public function &getView($name)
        {
            if ($this->viewExists($name))
            {
                return $this->views[$name];
            }
            else
            {
                return null;
            }
        }

        public function removeWidget($name)
        {
            unset($this->views[$name]);
            return $this;
        }
    }

?>
