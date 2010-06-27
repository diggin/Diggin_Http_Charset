<?php
set_include_path(dirname(__FILE__).'/library' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
//Zend_Loader_Autoloader::getInstance();
require_once 'Diggin/Http/Response/Charset/Front/UrlRegex.php';

$header = "HTTP/1.1 200 OK" ."\r\n".
           "Content-type: text/html; charset=Shift-JIS;"; // ... or SJIS-win ?
$html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=Shift-JIS" http-equiv="Content-Type" />
</head>
<body>
<!--① ㈱① ㈱-->ああ
</body>
HTML;

// 28.05 2010 added
$html = mb_convert_encoding($html, 'SJIS-win', 'UTF-8');
//$html = mb_convert_encoding($html, 'Shift-JIS', 'UTF-8');
$response = Zend_Http_Response::fromString("$header\r\n\r\n$html");


$front = new Diggin_Http_Response_Charset_Front_UrlRegex;
$content = array('body' => $response->getBody(), 
                 'content-type' => $response->getHeader('content-type'));
$ret = $front->encode(array('url' => 'test', 'content' => $content));

class My_Encoder extends Diggin_Http_Response_Charset_Encoder_EncoderAbstract
{
    const URL_REGEX = '#^http://github.com/*#s';

    protected function _encodingfrom($body, $ctype)
    {
        //return 'Shift-JIS';
        return 'UTF-8';
    }
}

//$front->addEncoder(My_Encoder::URL_REGEX, new My_Encoder);
$front->addEncoder(My_Encoder::URL_REGEX, new Diggin_Http_Response_Charset_Encoder_Fixedfrom('SJIS-win'));

$ret = $front->encode(array('url' => 'http://github.com/test', 'content' => $content));

var_dump($ret);
