<?php

namespace DigginTest\Http\Charset\Detector;
use Diggin\Http\Charset\Detector\HtmlDetector;

class HtmlDetectorTest extends \PHPUnit_Framework_TestCase
{

    private $detectOrder;

    /**
     * "detect" when only response body available
     */
    public function testDetectOnlyResponseBody() 
    {
        $detector = new HtmlDetector();

        $this->assertEquals('SJIS-win',
                            $detector->detect(pack("C2", 0x87, 0x40)));

        //@see http://homepage2.nifty.com/Catra/memo/perl_pack.html
        $this->assertEquals('eucJP-win',
                            $detector->detect(pack("C4", 164, 164, 164, 164)));
                            
        //if parameter has non-AlNum, must detect as UTF-8
        $this->assertEquals('UTF-8',
                            $detector->detect('あ1ab'));

        $detector->setConfig(array('detect_prefer_mime' => true));
        $this->assertEquals('Shift_JIS',
                            $detector->detect(pack("C2", 0x87, 0x40)));
    }
    
    public function testDetectWithMetaTag() 
    {
        $detector = new HtmlDetector();
        $detector->setConfig(array('accept_header_ctype' => true));
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
        $this->assertEquals('SJIS-win', $detector->detect($body));

        $eucjp_text = mb_convert_encoding('あ', 'EUC-JP', 'UTF-8');

$html5 = <<<HTML5
<!DOCTYPE html>
<html lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head><meta charset="EUC-JP" /><title>HTML5</title></head>
<body>$eucjp_text</body>
</html>
HTML5;
        $this->assertEquals('eucJP-win', $detector->detect($html5));
    }
    
    /**
     * With header test
     */
    public function testDetectWithHeadersContentType()
    {
        $detector = new HtmlDetector();
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

    public function testSetDetectOrder() 
    {
        $detector = new HtmlDetector();
        $this->assertEquals(HtmlDetector::DEFAULT_DETECT_ORDER,
                            $detector->getDetectOrder());
        
        $detectOrder = 'SJIS, UTF-8';
        $detector->setDetectOrder($detectOrder);
        
        $this->assertEquals($detectOrder, $detector->getDetectOrder());

        $detector->setDetectOrder(false);

        $this->assertEquals(HtmlDetector::DEFAULT_DETECT_ORDER,
                            $detector->getDetectOrder());
    }

    public function testIconvMap()
    {
        $detector = new HtmlDetector();
        $bodyCP949 = iconv('UTF-8', 'CP949', '플랫폼에서는');

        $charset = 'KS_C_5601-1987';
        $ctype = "text/html; charset=$charset;";
        $detect = $detector->detect($bodyCP949, $ctype);
        $this->assertEquals('CP949', $detect, 'detect should iconv handle available');
    }
}

