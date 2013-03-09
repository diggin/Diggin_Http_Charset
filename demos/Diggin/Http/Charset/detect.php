<?php
if(!isset($argv[1])) die('Error require url - USAGE:$php detect.php http://example.com');

include '../../../../vendor/autoload.php';
use Diggin\Http\Charset\Detector\HtmlDetector;

// main

$client = new Zend\Http\Client;
$client->setUri($argv[1]);
$response = $client->send();

$detector = new HtmlDetector();
$ret = $detector->detect($response->getBody(), $response->getHeaders()->get('Content-type')->getFieldValue());

var_dump($ret);

