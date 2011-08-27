<?php

if (isset($argv[1])) {
    set_include_path($argv[1]); // /path/to/ZendFramework-1.11.9-minimal/libray
}
require_once 'Zend/Http/Response.php';
include 'SplClassLoader.php';
$loader = new SplClassLoader('Diggin', '../../../../library');
$loader->register();
use Diggin\Http\Charset\WrapperFactory;

$header = "HTTP/1.1 200 OK" ."\r\n".
           "Content-Type: text/html; charset=Shift-JIS;"; // ... or SJIS-win ?
$html = <<<HTML
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=Shift-JIS" http-equiv="Content-Type" />
</head>
<body>
<!--① ㈱① ㈱-->ああ
</body>
HTML;

$html = mb_convert_encoding($html, 'SJIS-win', 'UTF-8');
$response = Zend_Http_Response::fromString("$header\r\n\r\n$html");
$wrap = WrapperFactory::factory($response);

echo '[converted response body]', PHP_EOL, PHP_EOL;
var_dump($wrap->getBody());
