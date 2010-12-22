<?php
    header('Content-type: text/html;charset=UTF-8');
?><pre><?php
    error_reporting(-1);
    ini_set('display_errors', 1);
    chdir(dirname(__FILE__));

    require_once 'system/init.php';

    require_once ICMS_SYS_PATH . 'lib/tools/debug.php';
    require_once ICMS_SYS_PATH . 'lib/request/router/modrewriterouter.php';

    Registry::set('designpath', ICMS_SYS_PATH . 'designs/');
    Registry::set('logpath', ICMS_SYS_PATH . 'logs/frontend/');
    Registry::set('languagepath', ICMS_SYS_PATH . 'language/frontend/');
    Registry::set('templatepath', ICMS_SYS_PATH . 'templates/frontend/');
    

    try
    {
        $frontcontroller = new Frontcontroller();
        $frontcontroller->setControllerPath(ICMS_SYS_PATH . 'controller/frontend/');
        $request = Request::getInstance();
        $response = Response::getInstance();
        $request->route(new ModRewriteRouter());
        
        $time = Debug::benchmark(array($frontcontroller, 'run'), array($request, $response), $result);

        echo "\n\nRuntime: $time seconds\n\n\n\n";
    }
    catch (Exception $e)
    {
        echo "EXCEPTION !!!!\nMessage: " . $e->getMessage();
    }
    
?>
</pre>