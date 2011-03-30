<?php

if(!isset($argv[1])) die('Error require url - USAGE:$php detect.php http://example.com');

set_include_path(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/library/'. PATH_SEPARATOR. get_include_path());

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);

$client = new Zend_Http_Client;
$client->setUri($argv[1]);
$response = $client->request();

$detector = new Diggin_Http_Response_Charset_Detector_Html();
//if($argv[2] == '1') $detector->setConfig(array('force_detect_body' => false));
$ret = $detector->detect($response->getBody(), $response->getHeader('Content-type'));

var_dump($ret);

