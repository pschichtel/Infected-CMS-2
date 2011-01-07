<?php

    require_once 'system/init.php';
    require_once ICMS_SYS_PATH . 'lib/debugging/debug.php';
    require_once ICMS_SYS_PATH . 'lib/debugging/log.php';
    require_once ICMS_SYS_PATH . 'lib/image/imagemanipulate.php';
    define('DITHER_LOG', dirname(__FILE__) . '/ditherlog.txt');

    function log_error($errno, $errstr ,$errfile, $errline, $errcontext)
    {
        $logger = Log::factory(DITHER_LOG);
        $logger->write(0, 'error', '[' . basename($errfile) . ':' . $errline . '] ' . $errstr);
    }

    function dither()
    {
        $image = new ImageManipulate('frau.png');
        //$image->dither(array('255,255,255', '204,204,204', '153,153,153', '0,0,0', '255,0,0', '255,0,0', '255,255,0', '153,153,0', '0,255,0', '0,153,0', '0,255,255', '0,153,153', '0,0,255', '0,0,153', '255,0,255', '153,0,153'));
        //$image->dither(array('255,255,255', '0,0,0'));
        $image->dither(array('255,0,0', '0,0,255'));
        $image->rescaleByHeight(300);
        header('Content-type: ' . $image->getMime());
        echo $image->render();
    }

    error_reporting(-1);
    set_error_handler('log_error', -1);


    try
    {
        $logger = Log::factory(DITHER_LOG);

        $time = Debug::benchmark('dither');
        echo basename(__FILE__) . ':' . __LINE__ . " reached!<br />\n";
        $logger->write(0, 'runtime', $time . '');
    }
    catch (Exception $e)
    {
        echo $e->getMessage();
    }
    
?>
