<?php
    //header('Content-type: text/html;charset=UTF-8');
?><pre><?php
    error_reporting(-1);
    ini_set('display_errors', 1);
    chdir(dirname(__FILE__));

    require_once 'system/init.php';

    require_once ICMS_SYS_PATH . 'lib/debugging/debug.php';

    Registry::set('designpath',     ICMS_SYS_PATH . 'designs/');
    Registry::set('logpath',        ICMS_SYS_PATH . 'logs/frontend/');
    Registry::set('languagepath',   ICMS_SYS_PATH . 'language/frontend/');
    Registry::set('templatepath',   ICMS_SYS_PATH . 'templates/frontend/');

    //$config = new EncryptedConfigFile(ICMS_SYS_PATH . 'configs/database.encrypted.conf', 'supersicher');
    //$config = new INIConfigFile(ICMS_SYS_PATH . configs/database.conf');
    $config = new ConfigFile(ICMS_SYS_PATH . 'configs/database.conf');
    Registry::set('database',       Database::factorFromConfig($config));
    //Registry::set('database',       Database::factory('mysql://root@localhost/test?ci-0001_#utf8'));

    /*$config->setConfig(array(
        'adapter' => 'mysql',
        'user' => 'root',
        'pass' => '',
        'host' => 'localhost',
        'database' => 'test',
        'prefix' => 'ci-0001_',
        'charset' => 'utf8'
    ));
    $config->save();*/
    var_dump($config->getAll());
    

    try
    {
        $frontcontroller = new Frontcontroller();
        $frontcontroller->setControllerPath(ICMS_SYS_PATH . 'pages/frontend/');
        $request = Request::getInstance();
        $response = Response::getInstance();
        $request->route(new DefaultRouter());
        
        $time = Debug::benchmark(array($frontcontroller, 'run'), array($request, $response), $result);

        echo "\n\nRuntime: $time seconds\n\n\n\n";
    }
    catch (Exception $e)
    {
        echo "EXCEPTION !!!!\nMessage: " . $e->getMessage() . "\nAt:" . basename($e->getFile()) . ':' . $e->getLine();
    }
    
?>
</pre>