<?php
    defined('DS')               or define('DS', DIRECTORY_SEPARATOR);
    defined('ICMS_SYS_PATH')    or define('ICMS_SYS_PATH',  dirname(__FILE__) . DS);

    require_once ICMS_SYS_PATH . 'lib/Util/Loader.php';
    Loader::registerAutoloader();
    Loader::addClassMap();

    Registry::set('tpl.design_path',    ICMS_SYS_PATH . 'designs/');
    Registry::set('log_path',           ICMS_SYS_PATH . 'logs/frontend/');
    Registry::set('language_path',      ICMS_SYS_PATH . 'language/frontend/');

?>