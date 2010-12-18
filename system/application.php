<?php
    defined('DS')               or define('DS', DIRECTORY_SEPARATOR);
    defined('IFW_CORE_PATH')    or define('IFW_CORE_PATH',  dirname(__FILE__) . DS);

    Application::import('events::eventmanager');
    Application::import('application::iapplication');
    Application::import('application::appconfig');
    Application::import('data::statictypes::staticarray');
    
    EventManager::registerEvent(new Event('onBeforeApplicationExecution'));
    EventManager::registerEvent(new Event('onAfterApplicationExecution'));

    class Application
    {
        public static $includeCache = array();
        private static $application = null;
        //private static $appConfig;
        private static $appPaths = null;

        public static function run(IApplication $application, StaticArray $config = null)
        {
            set_include_path(IFW_CORE_PATH);
            self::$application = $application;
            //self::$appConfig = $config;
            EventManager::triggerEvent('onBeforeApplicationExecution', null, EventArgs::factory(array('timestamp' => microtime())));
            self::$application->run();
            EventManager::triggerEvent('onAfterApplicationExecution', null, EventArgs::factory(array('timestamp' => microtime())));
        }

        public static function appPaths($name)
        {

        }

        /**
         * imports a files from the framework includepath
         *
         * @access public
         * @static
         * @param string $path
         * @return bool true if the import succeeded, otherwise false. (false can also indicate that the files was already imported)
         */
        public static function import($path)
        {
            if (!isset(self::$includeCache[$path]))
            {
                if (strpos($path, '.') === false)
                {
                    $filepath = IFW_CORE_PATH . str_replace('::', DIRECTORY_SEPARATOR, strtolower($path)) . '.php';
                    if (file_exists($filepath))
                    {
                        self::$includeCache[$path] = true;
                        require $filepath;
                        return true;
                    }
                }
            }
            return false;
        }

        public static function isFileLoaded($path)
        {
            return isset(self::$includeCache[$path]);
        }
    }

?>