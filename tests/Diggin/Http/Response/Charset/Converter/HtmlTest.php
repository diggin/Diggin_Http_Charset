<?php

require_once 'PHPUnit/Framework.php';
require_once 'Diggin/Http/Response/Charset/Converter/Html.php';

class Diggin_Http_Response_Charset_Converter_HtmlTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {}

    protected function tearDown()
    {}

    /**
     */
    public function testLoadDetector() 
    {
        $converter = new Diggin_Http_Response_Charset_Converter_Html;
        $this->assertEquals('Diggin_Http_Response_Charset_Detector_Html', 
                            get_class($converter->getDetector()));
    }
    

    public function testConvertStandard()
    {
$html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta content="text/html; charset=Shift-JIS" http-equiv="Content-Type" />
</head>
<body><span>あああ</span></body>
HTML;

        
        $sjishtml = mb_convert_encoding($html, 'Shift-JIS', 'UTF-8');

        $converter = new Diggin_Http_Response_Charset_Converter_Html;

        $ret = $converter->convert(array('body' => $sjishtml, 
                                          'content-type' => 'text/html'));
        $this->assertEquals($html, $ret);


        list($h, $r) = $converter->convert(array('body' => $sjishtml, 
                                                         'content-type' =>  'text/html'),
                                                  array(mb_convert_encoding('ああ', 'Shift-JIS', 'UTF-8')));

        $this->assertEquals($html, $h);
        $this->assertEquals(array('ああ'), $r);
    }

    public function testConvertAgainstMimeCharset()
    {
$header = "HTTP/1.1 200 OK" ."\r\n".
           "Content-type: text/html; charset=Shift-JIS"; // ... liar!! it is not mimename(Shift_JIS) & html include SJIS-win

$html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta content="text/html; charset=Shift-JIS" http-equiv="Content-Type" />
</head>
<body><!--① ㈱① ㈱-->ああ</body>
HTML;
        $sjisstring = mb_convert_encoding($html, 'SJIS-win', 'UTF-8');

        $converter = new Diggin_Http_Response_Charset_Converter_Html;

        $ret = $converter->convert(array('body' => $sjisstring, 
                                          'content-type' => 'text/html; charset=Shift-JIS'));

        $this->assertEquals($html, $ret);

    }

    public function testConvertNotAvailableForMbstring()
    {
        //$header = "HTTP/1.1 200 OK" ."\r\n".
        //   "Content-type: text/html; charset=TIS-620";

$html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=TIS-620">
</head>
<body>สวัสดี</body>
HTML;

        $tis620 = iconv('UTF-8', 'TIS-620', $html);

        $converter = new Diggin_Http_Response_Charset_Converter_Html;

        $ret = $converter->convert(array('body' => $tis620, 
                                          'content-type' => 'text/html; charset=TIS-620'));

        $this->assertEquals($html, $ret);

    }
}


