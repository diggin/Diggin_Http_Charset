<?php

namespace DigginTest\Http\Charset\Converter;
use Diggin\Http\Charset\Converter\FixedfromConverter;

class FixedfromConverterTest extends \PHPUnit_Framework_TestCase
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
  
        $converter = new FixedfromConverter('EUC-JP');

        $ret = $converter->convert($eucjptext);
        $this->assertEquals('あいうえお', $ret);

    }

}


