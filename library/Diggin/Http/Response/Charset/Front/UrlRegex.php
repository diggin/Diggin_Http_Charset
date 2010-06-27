<?php

/**
 * Diggin_Http_Response_Charset
 * 
 * a part of this package (Diggin_Http_Response_Charset_Detector_Html) is
 * borrowed from HTMLScraping
 * 
 * @see http://www.rcdtokyo.com/etc/htmlscraping/
 *
 * LICENSE: This source file is subject to the GNU Lesser General Public
 * License as published by the Free Software Foundation;
 * either version 2.1 of the License, or any later version
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl.html
 * If you did not have a copy of the GNU Lesser General Public License
 * and are unable to obtain it through the web, please write to
 * the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 * ---------------------------------------------------------------------
 */

require_once 'Diggin/Http/Response/Charset/Front/EncodeInterface.php';
require_once 'Diggin/Http/Response/Charset/Encoder/EncoderInterface.php';

class Diggin_Http_Response_Charset_Front_UrlRegex 
    implements Diggin_Http_Response_Charset_Front_EncodeInterface
{
    /**
     * @var Diggin_Http_Response_Charset_Encoder_EncoderInterface
     */
    static private $_defaultEncoder;

    /**
     * @var array
     */
    protected $_encoderSet = array();

    /**
     * Encode
     *
     * @todo implements handle Diggin_Spider_Document
     *
     * @param array $document
     * @param array $remains
     * @return mixed string|array
     */
    public function encode($document, $remains = null)
    {
        if (is_array($document)) {
            $url = $document['url'];
            $content = $document['content'];
        } elseif (is_string($document)) {
            $url = '';
            $content = $document;
        } else {
            require_once 'Diggin/Http/Response/Charset/Front/Exception.php';
            throw new Diggin_Http_Response_Charset_Front_Exception('Invalid Argument');
        }
        // and remaind args will use backup var.

        if ($encoder = $this->_checkMatch((string)$url)) {
            return $encoder->encode($content, $remains);
        }

        return $this->getDefaultEncoder()->encode($content, $remains);
    }

    /**
     * add Encoder
     * $pattern should be preg-regex
     *
     * @param string $pattern
     * @param Diggin_Http_Response_Charset_Encoder_EncoderInterface
     * @return Diggin_Http_Response_Charset_Front_UrlRegex
     */
    public function addEncoder($pattern, Diggin_Http_Response_Charset_Encoder_EncoderInterface $encoder)
    {
        $this->_encoderSet[$pattern] = $encoder;

        return $this;
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
