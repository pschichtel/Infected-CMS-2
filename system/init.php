<?php
    defined('DS')               or define('DS', DIRECTORY_SEPARATOR);
    defined('ICMS_CORE_PATH')   or define('ICMS_CORE_PATH',  dirname(__FILE__) . DS);

    require_once ICMS_CORE_PATH . 'lib/controller/frontcontroller.php';
    require_once ICMS_CORE_PATH . 'lib/events/eventmanager.php';
    require_once ICMS_CORE_PATH . 'lib/tools/debug.php';
    
    EventManager::registerEvent(new Event('onBeforeAppExec'));
    EventManager::registerEvent(new Event('onAfterAppExec'));

    $db = Database::factory('mysql://root@localhost/test');
    $qb = new mysqlQuery();
    $result = $db->query($qb->select_from('test'));

    $time = Debug::benchmark(array($result, 'fetchAll'), array(), $return);
    var_dump($return);
    echo "\n\nruntime: $time seconds";


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