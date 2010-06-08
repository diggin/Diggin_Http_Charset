<?php
set_include_path(dirname(__FILE__).'/library' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
//Zend_Loader_Autoloader::getInstance();
require_once 'Diggin/Http/Response/Charset/Front/UrlRegex.php';

$html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta content="text/html; charset=Shift-JIS" http-equiv="Content-Type" />
<body>
<span>あああ</span></body>
HTML;

$html = mb_convert_encoding($html, 'Shift-JIS', 'UTF-8');
$adapter = new Zend_Http_Client_Adapter_Test();
$adapter->setResponse(
    "HTTP/1.1 200 OK"        . "\r\n" .
    "Content-type: text/html" . "\r\n" .
                               "\r\n" .
    $html);
$client = new Zend_Http_Client($url = 'http://www.yahoo.jp', array('adapter' => $adapter));

$front = new Diggin_Http_Response_Charset_Front_UrlRegex;
$response = $client->request();
$resouce = array('body' => $response->getBody(), 
                 'content-type' => $response->getHeader('content-type'));
$remains = mb_convert_encoding('ああ', 'Shift-JIS', 'UTF-8');
$ret = $front->encode(array('url' => 'test', 'resouce' => $resouce), array($remains));
$ret = $front->encode(array('url' => 'test', 'resouce' => $resouce));

var_dump($ret);

class My_Encoder extends Diggin_Http_Response_Charset_Encoder_EncoderAbstract
{
    const URL_REGEX = '#^http://github.com/*#s';

    protected function _encodingfrom($body, $ctype)
    {
        //return 'Shift-JIS';
        return 'UTF-8';
    }
}

$front->addEncoder(My_Encoder::URL_REGEX, new My_Encoder);

$ret = $front->encode(array('url' => 'http://github.com/test', 'resouce' => $resouce), array($remains));

var_dump($ret);
