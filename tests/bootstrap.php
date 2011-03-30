<?php

error_reporting( E_ALL | E_STRICT );
set_include_path(dirname(dirname(__FILE__)).'/library/'. PATH_SEPARATOR. get_include_path());

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()
    ->registerNamespace('PHPUnit_')
    ->registerNamespace('Diggin_');
