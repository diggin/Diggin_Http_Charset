<?php

require_once 'PHPUnit/Framework.php';
require_once 'Diggin/Http/Response/Charset/Front/UrlRegex.php';

class Diggin_Http_Response_Charset_Front_UrlRegexTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {}

    protected function tearDown()
    {}

    public function testConvert()
    {
        $contentType = 'text/html; charset=ISO-8859-1';

        $iso8859_1 = mb_convert_encoding('é', 'ISO-8859-1', 'UTF-8');

        $front = new Diggin_Http_Response_Charset_Front_UrlRegex;
        $content = array('body' => $iso8859_1, 
                          'content-type' => $contentType);

        $ret = $front->convert(array('url' => 'http://example.com/aa', 'content' => $content));
        $this->assertEquals('é', $ret);

        $ret = $front->convert(array('url' => 'http://example.com/aa', 'content' => array('body' => $iso8859_1)));
        $this->assertNotEquals('é', $ret);

        $front->addConverter('#http://example.com/aa/*#', new Diggin_Http_Response_Charset_Converter_Fixedfrom('ISO-8859-1'));        
        $ret = $front->convert(array('url' => 'http://example.com/aa/1', 'content' => array('body' => $iso8859_1)));
        $this->assertEquals('é', $ret);

        $front->addConverter('#http://example.com/bb/*#', new Diggin_Http_Response_Charset_Front_UrlRegexTest_Converter);
        
        $mixed = '01234<body>'.mb_convert_encoding('①', 'SJIS-win', 'UTF-8');
        $ret = $front->convert(array('url' => 'http://example.com/bb/1', 'content' => array('body' => $mixed)));
        $this->assertEquals('01234<body>①', $ret);

        $front->addConverter('#http://example.com/func/*#', 
                             function($content) {return mb_convert_encoding($content['body'], 'UTF-8', 'Shift-JIS');});
        $ret = $front->convert(array('url' => 'http://example.com/func/1', 
                                     'content' => array('body' => mb_convert_encoding('あいうえお', 'Shift-JIS', 'UTF-8'))));
        $this->assertEquals('あいうえお', $ret);

    }

}

class Diggin_Http_Response_Charset_Front_UrlRegexTest_Converter
    extends Diggin_Http_Response_Charset_Converter_ConverterAbstract
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

