<?php
namespace DigginTest\Http\Charset;
use Diggin\Http\Charset\WrapperFactory;
use Diggin\Http\Charset\Wrapper\ZF2Wrapper;

class WrapperFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testZF2()
    {

        $header = "HTTP/1.1 200 OK" ."\r\n".
                  "Content-Type: text/html; charset=Shift-JIS";
        $html = <<<EOF
            <html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
            <head><meta content="text/html; charset=Shift-JIS" http-equiv="Content-Type" /></head>
            <body><!--① ㈱① ㈱-->ああ</body>
EOF;

        $sjis = mb_convert_encoding($html, 'SJIS-win', 'UTF-8');

        $response = \Zend\Http\Response::fromString("$header\r\n\r\n$sjis");


        $wrapper = WrapperFactory::factory($response);
        
        $this->assertInstanceOf('Zend\\Http\\Response', $wrapper);
        $this->assertInstanceOf('Diggin\\Http\Charset\\Wrapper\\ZF2Wrapper', $wrapper);
        $this->assertEquals($html, $wrapper->getBody());
    }

    public function testSymfony2BrowserKit()
    {
        $html = <<<EOF
            <html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
            <head><meta content="text/html; charset=Shift-JIS" http-equiv="Content-Type" /></head>
            <body><!--① ㈱① ㈱-->ああ</body>
EOF;

        $sjis = mb_convert_encoding($html, 'SJIS-win', 'UTF-8');

        $response = new \Symfony\Component\BrowserKit\Response($sjis, 200, array('Content-Type' => 'text/html; charset=Shift-JIS'));

        $response = WrapperFactory::factory($response);
        
        $this->assertInstanceOf('Symfony\Component\BrowserKit\Response', $response);
        $this->assertInstanceOf('Diggin\\Http\Charset\\Wrapper\\Symfony2', $response);
        $this->assertEquals($html, $response->getContent());
    }
    
}

