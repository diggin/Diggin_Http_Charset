<?php
require_once 'Diggin/Http/Response/Charset/Front/EncodeInterface.php';
require_once 'Diggin/Http/Response/Charset/Encoder/EncoderInterface.php';
class Diggin_Http_Response_Charset_Front_UrlRegex 
    implements Diggin_Http_Response_Charset_Front_EncodeInterface
{
    static private $_defaultEncoder;
    protected $_encoderSet = array();

    public function encode($document, $remains = null)
    {

        //if (!$document instanceof Diggin_Scraper_Document)
        if (is_array($document)) {
            $url = $document['url'];
            $resouce = $document['resouce'];
        } elseif (is_string($document)) {
            $url = '';
            $resouce = $document;
        } else {
            require_once 'Diggin/Http/Response/Charset/Front/Exception.php';
            throw new Diggin_Http_Response_Charset_Front_Exception('Invalid Argument');
        }
        // and remaind args will use backup var.

        if ($encoder = $this->_checkMatch((string)$url)) {
            return $encoder->encode($resouce, $remains);
        }

        return $this->getDefaultEncoder()->encode($resouce, $remains);
    }

    public function addEncoder($pattern, Diggin_Http_Response_Charset_Encoder_EncoderInterface $encoder)
    {
        $this->_encoderSet[$pattern] = $encoder;
    }

    public function getEncoderSet()
    {
        return $this->_encoderSet;
    }

    protected function _checkMatch($url)
    {
        foreach ($this->getEncoderSet() as $pattern => $encoder) {
            if (preg_match($pattern, $url)) {
                return $encoder;
            }
        }
    }

    final static public function getDefaultEncoder()
    {
        if (!self::$_defaultEncoder) {
            require_once 'Diggin/Http/Response/Charset/Encoder/Html.php';
            self::$_defaultEncoder = new Diggin_Http_Response_Charset_Encoder_Html;
        }

        return self::$_defaultEncoder;
    }

}
