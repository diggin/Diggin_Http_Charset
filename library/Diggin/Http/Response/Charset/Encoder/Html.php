<?php
require_once 'Diggin/Http/Response/Charset/Encoder/EncoderAbstract.php';
class Diggin_Http_Response_Charset_Encoder_Html
    extends Diggin_Http_Response_Charset_Encoder_EncoderAbstract
{
    private $_detector;

    protected function _encodingFrom($body, $ctype)
    {
        return $this->getDetecter()->detect($body, $ctype);
    }

    final public function getDetecter()
    {
        if (!$this->_detector) {
            require_once 'Diggin/Http/Response/Charset/Detector/Html.php';
            $this->_detector = new Diggin_Http_Response_Charset_Detector_Html();
        }

        return $this->_detector;
    }
}

