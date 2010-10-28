<?php

require_once 'PHPUnit/Framework.php';
require_once 'Diggin/Http/Response/Charset/Converter/Fixedfrom.php';

class Diggin_Http_Response_Charset_Converter_FixedfromTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {}

    protected function tearDown()
    {}
   

    public function testConvert()
    {
$plain = <<<PLAIN
あいうえお
PLAIN;

        $eucjptext = mb_convert_encoding($plain, 'EUC-JP', 'UTF-8');
  
        $converter = new Diggin_Http_Response_Charset_Converter_Fixedfrom('EUC-JP');

        $ret = $converter->convert(array('body' => $eucjptext));
        $this->assertEquals('あいうえお', $ret);

    }

}


