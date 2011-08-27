<?php

// JAPANESE:
// この例は、ブラウザでも文字化けが起こる場合での対応方法です。
// _initBody()に正規表現で文字コードが対応しない箇所をmb_convert_encodingします。
//
// 
// p2-phpのソースはsjisのため実際にブラウザでも化けます。
// なおこのコンバータは2011/07/27での動作確認済みです

//$url = 'https://github.com/unpush/p2-php/blob/master/cookie.php';
$url = 'https://github.com/unpush/p2-php/blob/ced27b81d555a4f825c3d862a1042c476dd9a4f0/cookie.php';

include 'SplClassLoader.php';

if (isset($argv[1])) {
    set_include_path($argv[1]); // /path/to/ZendFramework-1.11.9-minimal/libray
}
require_once  'Zend/Http/Client.php';

$loader = new SplClassLoader('Diggin', dirname(dirname(dirname(dirname(__DIR__)))).'/library');
$loader->register();

use Diggin\Http\Charset\Converter\AbstractConverter,
    Diggin\Http\Charset\Front\UrlRegex;

class My_Converter extends AbstractConverter
{
    const URL_REGEX = '#^https://github.com/*#s';

    protected function _initBody($body)
    {
        return $ret = preg_replace_callback(
                        '#<div class=\"data type\-php\">(.*?)</table>.*</div>#is',
                  function($m){
                  //var_dump($m);
                  return '<div class="data type-php">'. 
                                         mb_convert_encoding($m[1], 'UTF-8', 'SJIS-win').
                                         "</table></div>";},
                  $body);
    }

    protected function _encodingfrom($body, $ctype)
    {
        return 'UTF-8';
    }
}

$front = new UrlRegex;
$front->addConverter(My_Converter::URL_REGEX, new My_Converter);

$client = new Zend_Http_Client($url);
$response = $client->request();

$content = array('body' => $response->getBody(), 
                 'content-type' => $response->getHeader('content-type'));
$ret = $front->convert(array('url' => $client->getUri(), 'content' => $content));

var_dump(preg_replace('/\s*/', '', strip_tags($ret)));
