<?php

/**
 * Original code borrowed from HTMLScraping
 * 
 * @see http://www.rcdtokyo.com/etc/htmlscraping/
 *
 * ---------------------------------------------------------------------
 * HTMLScraping class
 * ---------------------------------------------------------------------
 * PHP versions 5 (5.1.3 and later)
 * ---------------------------------------------------------------------
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

/**
 * Diggin - Simplicity PHP Library
 * 
 * @category   Diggin
 * @package    Diggin_Http
 * @subpackage Response
 */
class Diggin_Http_Response_Charset_Detector_Html
{
    const DETECT_ORDER = 'ASCII, JIS, UTF-8, EUC-JP, SJIS';

    /**
     * @var string $_detectOrder
     */
    private static $_detectOrder = 'ASCII, JIS, UTF-8, EUC-JP, SJIS';

    private $_config = array('accept_header_ctype' => true,
                             'force_detect_body' => true,
                             'prefer_mime_name' => false);

    
    /**
     * Set detect-order (static)
     *
     * @param string $order
     */
    public static function setDetectOrder($order)
    {
        if ($order === false) {
            self::$_detectOrder = self::DETECT_ORDER;
        } else {
            self::$_detectOrder = $order;
        }
    }

    /**
     * Get detecting order
     *
     * @return string
     */
    public static function getDetectOrder()
    {
        return self::$_detectOrder;
    }


    
    /**
     * Detect response's character code name
     *
     * @param string $responseBody
     * @param string $contentType
     * @return string $encoding
     */
    public function detect($responseBody, $contentType = null)
    {
        $encoding = false;
        if (isset($contentType)) {
            $encoding = self::_getCharsetFromCType($contentType);
        }
        if ((!$encoding or (!$this->_config['accept_header_ctype']))
                and preg_match_all('/<meta\b[^>]*?>/si', $responseBody, $matches)) {
            foreach ($matches[0] as $value) {
                if (strtolower(self::_getAttribute('http-equiv', $value)) == 'content-type'
                    and false !== $encoding = self::_getAttribute('content', $value)) {
                    $encoding = self::_getCharsetFromCType($encoding);
                    break;
                }
            }
        }

        /*
         * Use mbstring to detect character encoding if available.
         */
        if (extension_loaded('mbstring') and 
            (!$encoding or (!$this->_config['force_detect_body']))) {
            $detectOrder = mb_detect_order();
            mb_detect_order(self::getDetectOrder());
            $detect = @mb_detect_encoding($responseBody);
            mb_detect_order($detectOrder);unset($detectOrder);//restore

            if ($detect) {
                if ($this->_config['prefer_mime_name']) {
                    $detect = @mb_preferred_mime_name($detect);
                    if (!$detect) {
                        require_once 'Diggin/Http/Response/Charset/Detector/Exception.php';
                        throw new Diggin_Http_Response_Charset_Detector_Exception('Failed preferre_mime_name.');
                    }
                }
                return $detect;
            }
            
            require_once 'Diggin/Http/Response/Charset/Detector/Exception.php';
            throw new Diggin_Http_Response_Charset_Detector_Exception('Failed detecting character encoding.');
        }
        
        return $encoding;
    }

    /**
     * Get Charset From Ctype
     * 
     * @param  string  $string
     * @return mixed
     */
    protected static function _getCharsetFromCType($string)
    {
        $array = explode(';', $string);
        /* array_walk($array, create_function('$item', 'return trim($item);')); */
        if (isset($array[1])) {
            $array = explode('=', $array[1]);
            if (isset($array[1])) {
                $charset = trim($array[1]);
                if (preg_match('/^UTF-?8$/i', $charset)) {
                    return 'UTF-8';
                } elseif (function_exists('mb_preferred_mime_name')) {
                    return @mb_preferred_mime_name($charset);
                } else {
                    return $charset;
                }
            }
        }
        return false;
    }

    /**
     * Get Attribute from meta-tags
     * 
     * @param string $name:
     * @param string $string:
     * @return mixed
     */
    protected static function _getAttribute($name, $string)
    {
        $search = "'[\s\'\"]\b".$name."\b\s*=\s*([^\s\'\">]+|\'[^\']+\'|\"[^\"]+\")'si";
        if (preg_match($search, $string, $matches)) {
            return preg_replace('/^\s*[\'\"](.+)[\'\"]\s*$/s', '$1', $matches[1]);
        } else {
            return false;
        }
    }
}
