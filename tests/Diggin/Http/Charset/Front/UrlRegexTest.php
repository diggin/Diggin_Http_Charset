<?php
namespace DigginTest\Http\Charset\Front;
use Diggin\Http\Charset\Front\UrlRegex,
    Diggin\Http\Charset\Converter\AbstractConverter;
use Diggin\Http\Charset\Converter\FixedfromConverter;

class UrlRegexTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {}

    protected function tearDown()
    {}

    public function testConvert()
    {
        $contentType = 'text/html; charset=ISO-8859-1';

        $iso8859_1 = mb_convert_encoding('é', 'ISO-8859-1', 'UTF-8');

        $front = new UrlRegex;

        $ret = $front->convert('e');
        $this->assertEquals('e', $ret, 'arg string convert');

        $ret = $front->convert($iso8859_1, array('content-type' => $contentType, 'url' => 'http://example.com/aa'));
        $this->assertEquals('é', $ret);

        $ret = $front->convert($iso8859_1, 'http://example.com/aa');
        $this->assertNotEquals('é', $ret);

        $front->addConverter('#http://example.com/aa/*#', new FixedfromConverter('ISO-8859-1'));
        $ret = $front->convert($iso8859_1, array('url' => 'http://example.com/aa/1'));
        $this->assertEquals('é', $ret);

        $front->addConverter('#http://example.com/bb/*#', new UrlRegexTestClass());
        
        $mixed = '01234<body>'.mb_convert_encoding('①', 'SJIS-win', 'UTF-8');
        $ret = $front->convert($mixed, array('url' => 'http://example.com/bb/1'));
        $this->assertEquals('01234<body>①', $ret);

        $front->addConverter('#http://example.com/func/*#', 
                             function($body, $metadata = array()) {return mb_convert_encoding($body, 'UTF-8', 'Shift-JIS');});
        $ret = $front->convert(mb_convert_encoding('あいうえお', 'Shift-JIS', 'UTF-8'),
                               array('url' => 'http://example.com/func/1'));
        $this->assertEquals('あいうえお', $ret);

    }

}

class UrlRegexTestClass extends AbstractConverter
{
    protected function _initBody($body)
    {
        $body = parent::_initBody($body);

        $utf8 = substr($body, 0 , 10);
        $re_encoded = mb_convert_encoding(substr($body, 10), 'UTF-8', 'SJIS-win');
        return $utf8 . $re_encoded;
    }

    protected function _encodingfrom($body, $ctype)
    {
        return 'UTF-8';
    }

}

