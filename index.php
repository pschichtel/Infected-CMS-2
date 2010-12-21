<?php
    header('Content-type: text/html;charset=UTF-8');
?><pre><?php
    error_reporting(-1);
    ini_set('display_errors', 1);
    chdir(dirname(__FILE__));

    require_once 'system/init.php';

    require_once ICMS_SYS_PATH . 'lib/tools/debug.php';
    require_once ICMS_SYS_PATH . 'lib/request/router/router.php';

    Registry::set('designpath', ICMS_SYS_PATH . 'designs/');
    Registry::set('logpath', ICMS_SYS_PATH . 'logs/frontend/');
    Registry::set('languagepath', ICMS_SYS_PATH . 'language/frontend/');
    Registry::set('templatepath', ICMS_SYS_PATH . 'templates/frontend/');
    


    $router = new Router(Request::getInstance());
    $frontcontroller = new Frontcontroller($router);
    $frontcontroller->setControllerPath(ICMS_SYS_PATH . 'controller/frontend/');
    try
    {
        $time = Debug::benchmark(array($frontcontroller, 'run'), array(Request::getInstance(), Response::getInstance()), $result);

        echo "\n\nRuntime: $time seconds\n\n\n\n";
    }
    catch (Exception $e)
    {
        echo "EXCEPTION !!!!\nMessage: " . $e->getMessage();
    }

        phpinfo();
    
?>
</pre>