<?php
set_include_path(dirname(__FILE__).'/library' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
require_once 'Diggin/Http/Response/Charset/Front/UrlRegex.php';

class My_Encoder extends Diggin_Http_Response_Charset_Encoder_EncoderAbstract
{
    const URL_REGEX = '#^http://github.com/*#s';

    protected function _initBody($body)
    {
        return $ret = preg_replace_callback(
                        '#<div class=\"data syntax type\-php\">(.*?)</table>\n\s*\n\s*</div>#is',
                  function($m){return '<div class="data syntax type-php">'. 
                                         mb_convert_encoding($m[1], 'UTF-8', 'Shift-JIS').
                                         "</table></div>";},
                  $body);
    }

    protected function _encodingfrom($body, $ctype)
    {
        return 'UTF-8';
    }
}

$front = new Diggin_Http_Response_Charset_Front_UrlRegex;
$front->addEncoder(My_Encoder::URL_REGEX, new My_Encoder);

$client = new Zend_Http_Client($argv[1]);
$response = $client->request();

$content = array('body' => $response->getBody(), 
                 'content-type' => $response->getHeader('content-type'));
$ret = $front->encode(array('url' => $client->getUri(), 'content' => $content));

var_dump(trim(strip_tags($ret)));
