<?php

// simply usage with file_get_contents()
// This example not require any other library (PEAR, Zend Framework..)

if(!isset($argv[1])) {
    $url = 'http://d.hatena.ne.jp/miyagawa/';
} else {
    $url = $argv[1];
}

$html = file_get_contents($url);

echo 'Headers...', PHP_EOL;
var_export($http_response_header); 
?>

<?php
echo 'Content-type is ...';
var_dump($cType = detect_contenttype_from_http_response_header($http_response_header));
?>


<?php
include 'SplClassLoader.php';
$loader = new SplClassLoader('Diggin', dirname(dirname(dirname(dirname(__DIR__)))).'/src');
$loader->register();

$front = new \Diggin\Http\Charset\Front\UrlRegex;

echo 'trying convert html without content-type..', PHP_EOL; sleep(1);
$converted_html = $front->convert($html);

var_dump(mb_substr(preg_replace('/\s*/s', '',strip_tags($converted_html)), 0, 800));
echo PHP_EOL, PHP_EOL;

echo 'trying convert html with content-type..', PHP_EOL; sleep(1);
$converted_html = $front->convert($html, array(
        'url' => $url, 
        'content-type' => $cType
        )
    );

var_dump(mb_substr(preg_replace('/\s*/s', '',strip_tags($converted_html)), 0, 800));

function detect_contenttype_from_http_response_header($http_response_header)
{
    foreach ($http_response_header as $v) {
        if (preg_match('/^Content-Type: (.*)/', $v, $m)) {
            return $m[1];
        }
    }
}
