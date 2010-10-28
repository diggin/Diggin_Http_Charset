<?php

require_once 'PHPUnit/Framework.php';
require_once 'Diggin/Http/Response/Charset.php';

class Diggin_Http_Response_CharsetTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {}

    protected function tearDown()
    {}

    public function testWrapper()
    {

        $header = "HTTP/1.1 200 OK" ."\r\n".
               "Content-Type: text/html; charset=Shift-JIS";
$html = <<<HTML
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=Shift-JIS" http-equiv="Content-Type" />
</head>
<body>
<!--① ㈱① ㈱-->ああ
</body>
HTML;
        $sjis = mb_convert_encoding($html, 'SJIS-win', 'UTF-8');
        require_once 'Zend/Http/Response.php';
        $response = Zend_Http_Response::fromString("$header\r\n\r\n$sjis");
        $wrap = Diggin_Http_Response_Charset::wrapResponse($response);
        $this->assertTrue($wrap instanceof Zend_Http_Response);
        
        $this->assertEquals($html, $wrap->getBody());

    }

    public function testClearHeadersCharset()
    {
        $header = "HTTP/1.1 200 OK" ."\r\n".
               "Content-Type: text/html; charset=Shift-JIS";

        require_once 'Zend/Http/Response.php';
        $response = Zend_Http_Response::fromString("$header\r\n\r\nABC");

        $c = Diggin_Http_Response_Charset::clearHeadersCharset($response->getHeaders());
        
        $this->assertEquals('text/html', $c['Content-type']);
    }

}

