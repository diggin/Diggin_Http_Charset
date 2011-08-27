<?php
namespace DigginTest\Http\Charset;
use Diggin\Http\Charset\WrapperFactory,
    Diggin\Http\Charset\Wrapper\ZF1Wrapper;

class WrapperFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testZF1()
    {
        $inc = include_once dirname(dirname(dirname(__DIR__))).'/vendor/ZF1/Zend/Http/Response.php';

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

        $response = \Zend_Http_Response::fromString("$header\r\n\r\n$sjis");

        $response = WrapperFactory::factory($response);
        
        $this->assertInstanceOf('Zend_Http_Response', $response);
        $this->assertInstanceOf('Diggin\\Http\Charset\\Wrapper\\ZF1Wrapper', $response);
        $this->assertEquals($html, $response->getBody());
    }

    
}

