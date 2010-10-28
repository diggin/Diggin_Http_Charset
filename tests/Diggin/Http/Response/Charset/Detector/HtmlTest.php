<?php

require_once 'PHPUnit/Framework.php';
require_once 'Diggin/Http/Response/Charset/Detector/Html.php';

class Diggin_Http_Response_Charset_Detector_HtmlTest extends PHPUnit_Framework_TestCase
{

    private $detectOrder;

    protected function setUp()
    {
        //$this->detectOrder = mb_detect_order();
    }

    protected function tearDown()
    {
        //mb_detect_order($this->detectOrder);
    }

    /**
     * test "detect" part.1
     */
    public function testDetectOnlyResponseBody() 
    {
        $detector = new Diggin_Http_Response_Charset_Detector_Html();

        $this->assertEquals('SJIS-win',
                            $detector->detect(pack("C2", 0x87, 0x40)));

        //@see http://homepage2.nifty.com/Catra/memo/perl_pack.html
        $this->assertEquals('eucJP-win',
                            $detector->detect(pack("C4", 164, 164, 164, 164)));
                            
        //if parameter has non-AlNum, must detect as UTF-8
        $this->assertEquals('UTF-8',
                            $detector->detect('あ1ab'));
    }
    
    public function testDetectWithMetaTag() 
    {
        $detector = new Diggin_Http_Response_Charset_Detector_Html();
        $sjis_text = mb_convert_encoding('あ', 'Shift_JIS', 'UTF-8');

$body = <<<BODY
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=Shift_JIS">
    <title>test</title>
    </head>
<body>$sjis_text
</body>
BODY;
        $this->assertEquals('SJIS-win',
                            $detector->detect($body));
    }
    
    /**
     * With header test
     */
    public function testDetectWithHeadersContentType()
    {
        $detector = new Diggin_Http_Response_Charset_Detector_Html();
        $ctype = "text/html; charset=utf-8;";       

        $bodyUTF8 = <<<BODY
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>title</title>
    </head>
<body>
あ
</body>
BODY;
        $bodySJIS = mb_convert_encoding($bodyUTF8, 'SJIS', 'UTF-8');
        
        $this->assertEquals('UTF-8',
                            $detector->detect($bodySJIS, $ctype));
    }

    /**
     *
     *
     */
    public function testSetDetectOrder() {
        
        $detector = new Diggin_Http_Response_Charset_Detector_Html();
        
        $this->assertEquals(Diggin_Http_Response_Charset_Detector_Html::DEFAULT_DETECT_ORDER,
                            $detector->getDetectOrder());
        
        $detectOrder = 'SJIS, UTF-8';
        $detector->setDetectOrder($detectOrder);
        
        $this->assertEquals($detectOrder,
                            $detector->getDetectOrder());


        $detector->setDetectOrder(false);

        $this->assertEquals(Diggin_Http_Response_Charset_Detector_Html::DEFAULT_DETECT_ORDER,
                            $detector->getDetectOrder());

    }

}


