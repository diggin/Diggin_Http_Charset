<?php
require_once 'Diggin/Http/Response/Charset/Encoder/EncoderAbstract.php';
class Diggin_Http_Response_Charset_Encoder_FixedFrom
    extends Diggin_Http_Response_Charset_Encoder_EncoderAbstract
{
    private $_encodingFrom;

    public function __construct($encodingFrom)
    {
        $this->_encodingFrom = $encodingFrom;
    }

    protected function _encodingFrom($body, $ctype)
    {
        return $this->_encodingFrom;
    }
}

