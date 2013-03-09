<?php
namespace DigginTest\Http\Charset;
use Diggin\Http\Charset\Filter;
use Zend\Http\Response;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function testClearHeadersCharset()
    {
        $header = "HTTP/1.1 200 OK" ."\r\n".
               "Content-Type: text/html; charset=Shift-JIS";

        $response = Response::fromString("$header\r\n\r\nABC");

        $c = Filter::clearHeadersCharset($response->getHeaders()->toArray());
        
        $this->assertEquals('text/html', $c['Content-Type']);
    }
}

