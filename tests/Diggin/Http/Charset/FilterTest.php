<?php
namespace DigginTest\Http\Charset;
use Diggin\Http\Charset\Filter;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function testClearHeadersCharset()
    {
        $header = "HTTP/1.1 200 OK" ."\r\n".
               "Content-Type: text/html; charset=Shift-JIS";

        $inc = include_once dirname(dirname(dirname(dirname(__DIR__)))).'/vendor/ZF1/Zend/Http/Response.php';

        $response = \Zend_Http_Response::fromString("$header\r\n\r\nABC");

        $c = Filter::clearHeadersCharset($response->getHeaders());
        
        $this->assertEquals('text/html', $c['Content-type']);
    }
}

