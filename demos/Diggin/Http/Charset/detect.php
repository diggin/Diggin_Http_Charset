<?php
if(!isset($argv[1])) die('Error require url - USAGE:$php detect.php http://example.com');

//ensure ZF1 include PATH & Charset library PATH
if (isset($argv[2])) {
    set_include_path($argv[2].PATH_SEPARATOR.dirname(dirname(dirname(dirname(__DIR__)))).'/library');
} else {
    set_include_path(get_include_path().PATH_SEPARATOR.dirname(dirname(dirname(dirname(__DIR__)))).'/library');
}

include_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
use Diggin\Http\Charset\Detector\HtmlDetector;

// main

$client = new Zend_Http_Client;
$client->setUri($argv[1]);
$response = $client->request();

$detector = new HtmlDetector();
$ret = $detector->detect($response->getBody(), $response->getHeader('Content-type'));

var_dump($ret);

