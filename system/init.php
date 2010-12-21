<?php
    defined('DS')               or define('DS', DIRECTORY_SEPARATOR);
    defined('ICMS_SYS_PATH')   or define('ICMS_SYS_PATH',  dirname(__FILE__) . DS);

    require_once ICMS_SYS_PATH . 'lib/events/eventmanager.php';
    require_once ICMS_SYS_PATH . 'lib/controller/frontcontroller.php';
    require_once ICMS_SYS_PATH . 'lib/models/registry.php';
    require_once ICMS_SYS_PATH . 'lib/request/request.php';
    require_once ICMS_SYS_PATH . 'lib/response/response.php';

?>