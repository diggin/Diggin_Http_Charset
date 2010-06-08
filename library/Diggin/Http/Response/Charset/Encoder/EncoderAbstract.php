<?php
require_once 'Diggin/Http/Response/Charset/Encoder/EncoderInterface.php';
abstract class Diggin_Http_Response_Charset_Encoder_EncoderAbstract
    implements Diggin_Http_Response_Charset_Encoder_EncoderInterface
{
    /**
     * encode response
     * 
     * @param mixed
     * Zend_Http_Response | array('content-type' => $var, 'body' => $html) | string $html
     * (@param string $convert_vars)
     * 
     * @return mixed
     */
    final public function encode($resouce, $remains = null)
    {
        //if ($resouce instanceof Zend_Http_Response)
        $ctype = null;
        if (is_array($resouce)) {
            $ctype = $resouce['content-type'];
            $body  = $resouce['body'];
        } elseif (is_string($resouce)) {
            $body = $resouce;
        }
        
        $encoding_from = $this->_encodingFrom($body, $ctype);
        $body = $this->_initBody($body);

        if ($remains) {
            @mb_convert_variables('UTF-8', $encoding_from, $body, $remains);

            return array($body, $remains);
        } else {
            $body = mb_convert_encoding($body, 'UTF-8', $encoding_from);
            return $body;
        }
    }

    abstract protected function _encodingFrom($body, $ctype);
    
    protected function _initBody($body)
    {
        return $body;
    }
}
