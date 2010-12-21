<?php
    /**
     * 
     */
    class Lang
    {
        /**
         * the array containung the language entries
         *
         * @access private
         * @var array
         */
        private $lang;

        /**
         * the language Log object
         *
         * @access private
         * @var Log
         */
        private $log;

        /**
         * the path which contains the language files
         *
         * @access protected
         * @var string the lang path
         */
        protected $langPath;

        /**
         * initiates the Lang object
         *
         * @access public
         * @param string $name the name of the language file
         */
        public function __construct($file)
        {
            $this->lang = array();
            $this->log = new Log('lang');
            $this->langPath = CI_APP_PATH . 'lang' . DIRECTORY_SEPARATOR;
            
            $this->log->write(1, 'init', 'file: ' . $file);
            $this->getLang($file);
        }

        /**
         * returns the language entry or an "not found"-text
         *
         * @access public
         * @param string $name the index of the language entry
         * @return string the content of the entry or the "not found"-text
         */
        public function __get($name)
        {
            return $this->getLangEntry($name);
        }

        /**
         * checks whether the language entry exists
         *
         * @global Info $info
         * @global Config $cfg
         * @param string $name the index of the language entry
         * @return bool true if it exists, otherwise false
         */
        public function __isset($name)
        {
            global $info, $cfg;
            if (isset($this->lang[$info->lang][$name]))
            {
                return true;
            }
            elseif (isset($this->lang[$cfg->cms_std_lang][$name]))
            {
                return true;
            }
            elseif (isset($this->lang[CI_STD_LANG][$name]))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * replaces placeholders in the language entry and returns the entry
         *
         * @access public
         * @param string $name index of the language entry
         * @param array $arguments array of the parameters passed at the call
         * @return string the language entry or a "not found"-text
         */
        public function __call($name, $arguments)
        {
            $entry = $this->getLangEntry($name);
            $this->log->write(2, 'info', 'Trying to replace ' . count($arguments) . ' params in entry "' . $name . '"');
            $vars = array();
            foreach ($arguments as $index => $value)
            {
                $vars[] = '%' . $index . '%';
            }
            return str_replace($vars, $arguments, $entry);
        }

        /**
         * loads all languages of the file
         *
         * @access private
         * @global Info $info
         * @param modul $modul the modul name passed by reference
         */
        private function getLang(&$file)
        {
            global $info;
            $langs = Listing::availableLangs();
            $this->log->write(2, 'info', 'Found ' . count($langs) . ' langs');
            
            foreach ($langs as &$token)
            {
                $file = $this->langPath . $langtag . DIRECTORY_SEPARATOR . $file . '.lang.php';
                if (file_exists($file))
                {
                    include($file);
                    if (isset($lang) && is_array($lang))
                    {
                        $this->lang[$langtag] = $lang;
                    }
                    unset($lang);
                }
            }
        }

        /**
         * returns the language entry from the best matching language
         * or at least a "not found"-text
         *
         * @access private
         * @global Info $info
         * @global Config $cfg
         * @param string $name the index of the language entry
         * @return string the entry or a "not found"-text
         */
        private function getLangEntry(&$name)
        {
            $cfg = &Config::getInstance();
            global $info;
            if (isset($this->lang[$info->lang][$name]))
            {
                $this->log->write(2, 'info', "Entry '$name' found in current lang");
                return $this->lang[$info->lang][$name];
            }
            elseif (isset($this->lang[$cfg->cms_std_lang][$name]))
            {
                $this->log->write(2, 'info', "Entry '$name' found in current default lang");
                return $this->lang[$cfg->cms_std_lang][$name];
            }
            elseif (isset($this->lang[CoreConfig::STD_LANG][$name]))
            {
                $this->log->write(2, 'info', "Entry '$name' found in core lang");
                return $this->lang[CoreConfig::STD_LANG][$name];
            }
            else
            {
                foreach ($this->lang as $token => &$lang)
                {
                    if (isset($lang[$name]))
                    {
                        $this->log->write(2, 'info', "Entry '$name' found in the lang '$token'");
                        return $lang[$name];
                    }
                }
                $this->log->write(1, 'error', "Entry '$name' not found in any configured lang");
                return htmlspecialchars('[LANG_ENTRY_"' . $name . '"_NOT_FOUND]');
            }
        }
    }
?>
