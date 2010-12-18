<?php
    defined('DS')               or define('DS', DIRECTORY_SEPARATOR);
    defined('ICMS_SYS_PATH')   or define('ICMS_SYS_PATH',  dirname(__FILE__) . DS);

    require_once ICMS_SYS_PATH . 'lib/controller/frontcontroller.php';
    require_once ICMS_SYS_PATH . 'lib/events/eventmanager.php';
    require_once ICMS_SYS_PATH . 'lib/data/registry.php';
    require_once ICMS_SYS_PATH . 'lib/tools/debug.php';


    /**
    class Application
    {

        public static function run(IApplication $application, StaticArray $config = null)
        {
            set_include_path(IFW_CORE_PATH);
            self::$application = $application;
            //self::$appConfig = $config;
            EventManager::triggerEvent('onBeforeApplicationExecution', null, EventArgs::factory(array('timestamp' => microtime())));
            self::$application->run();
            EventManager::triggerEvent('onAfterApplicationExecution', null, EventArgs::factory(array('timestamp' => microtime())));
        }
    }
    **/

?>