<?php

    function log_error($errno, $errstr ,$errfile, $errline, $errcontext)
    {
        $logger = Log::factory(DITHER_LOG);
        $logger->write(0, 'error', '[' . basename($errfile) . ':' . $errline . '] ' . $errstr);
    }
    set_error_handler('log_error', -1);

    require_once 'system/init.php';
    require_once ICMS_SYS_PATH . 'lib/debugging/debug.php';
    require_once ICMS_SYS_PATH . 'lib/debugging/log.php';
    require_once ICMS_SYS_PATH . 'lib/image/imagemanipulate.php';

    define('DITHER_LOG', 'ditherlog.txt');
    $logger = Log::factory(DITHER_LOG);

    function dither()
    {
        $image = new ImageManipulate('test.jpg');
        //$image->dither(array('255,0,0', '0,255,0', '0,0,255'));
        $image->dither(array('255,255,255', '0,0,0'));
        header('Content-type: ' . $image->getMime());
        echo $image->render();
    }

    $time = Debug::benchmark('dither');
    $logger->write(0, 'runtime', $time . '');
    
?>
