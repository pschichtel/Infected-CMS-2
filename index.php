<?php
    define('ENCODING', 'UTF8');
    header('Content-type: text/html;charset=' . ENCODING);
    mb_internal_encoding(ENCODING);
    
    error_reporting(-1);
    ini_set('display_errors', true);
    define('ICMS_APP_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
    chdir(ICMS_APP_PATH);

    require_once ICMS_APP_PATH . 'system/init.php';
    require_once ICMS_SYS_PATH . 'lib/debugging/debug.php';

    //$config = new EncryptedConfigFile(ICMS_SYS_PATH . 'configs/database.encrypted.conf', 'supersicher');
    $config = new INIConfigFile(ICMS_SYS_PATH . 'configs/database.conf');
    //$config = new ConfigFile(ICMS_SYS_PATH . 'configs/database.conf');
    $config->set('charset', ENCODING);
    Registry::set('database',       Database::factorFromConfig($config));
    //Registry::set('database',       Database::factory('mysql://root@localhost/test?ci-0001_#utf8'));

    Session::name('sessID');
    Session::lifetime(10);
    Session::instance();

    echo "<pre>";

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
    //var_dump($config->getAll());

    Template::addTemplatePath(ICMS_SYS_PATH . 'templates');


    try
    {
        $frontcontroller = new Frontcontroller();
        $frontcontroller->setControllerPath(ICMS_SYS_PATH . 'pages/frontend/');
        $request = Request::instance();
        $response = Response::instance();
        $request->route(new DefaultRouter());

        $time = Debug::benchmark(array($frontcontroller, 'run'), array($request, $response), $result);

        echo "\n\nRuntime: $time seconds\n\n\n\n";
    }
    catch (Exception $e)
    {
        echo "EXCEPTION !!!!\nMessage: " . $e->getMessage() . "\nAt:" . basename($e->getFile()) . ':' . $e->getLine();
    }
    
    //var_dump(Loader::getClassMap());

    echo '</pre>';

?>