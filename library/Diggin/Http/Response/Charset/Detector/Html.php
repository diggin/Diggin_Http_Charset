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

class Diggin_Http_Response_Charset_Detector_Html
{
    const DEFAULT_DETECT_ORDER = 'ASCII, JIS, UTF-8, eucJP-win, EUC-JP, SJIS-win, SJIS';

    /**
     * @var string $_detectOrder
     */
    private $_detectOrder = 'ASCII, JIS, UTF-8, eucJP-win, EUC-JP, SJIS-win, SJIS';

    private $_config = array('accept_header_ctype' => true,
                             'force_detect_body' => false,
                             'detect_prefer_mime' => false);
    
    /**
     * Set detect-order
     *
     * @param string $order
     */
    public function setDetectOrder($order)
    {
        if ($order === false) {
            $this->_detectOrder = self::DEFAULT_DETECT_ORDER;
        } else {
            $this->_detectOrder = $order;
        }
    }

    /**
     * Get detecting order
     *
     * @return string
     */
    public function getDetectOrder()
    {
        return $this->_detectOrder;
    }

    /**
     * Get list - Don't use mb_preferred_mime_name()
     * 
     * @return array
     */
    public function getListAgainstMime()
    {
        /**
        $preferrs = array();
        $couldnt = array();
        foreach(mb_list_encodings() as $k => $v) {
            if($p = @mb_preferred_mime_name($v)) {
                $preferrs[$k] = $p;
            } else {
                $couldnt[$k] = $v;
            }
        }

        $diff = array_diff(mb_list_encodings(), $preferrs);
        $diff = array_diff($diff, $couldnt);

        $mb_list_against_preferr_mime = array();
        foreach(array_flip($diff) as $v => $k){
            $mb_list_against_preferr_mime[$v] = $preferrs[$k];
        }
        var_export($mb_list_against_preferr_mime);
        */
        return array(
            'UUENCODE' => 'x-uuencode',
            'ASCII' => 'US-ASCII',
            'SJIS' => 'Shift_JIS',
            'eucJP-win' => 'EUC-JP',
            'SJIS-win' => 'Shift_JIS',
            'JIS' => 'ISO-2022-JP',
            'ISO-2022-JP-MS' => 'ISO-2022-JP',
            'EUC-CN' => 'CN-GB',
            'HZ' => 'HZ-GB-2312',
            'BIG-5' => 'BIG5',
        );
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
         * detect character encoding
         */
        if((in_array($encoding, $this->getListAgainstMime())) or (!$encoding or $this->_config['force_detect_body'])) {
            $detect = @mb_detect_encoding($responseBody, $this->getDetectOrder());
        
            if (in_array($encoding, $this->getListAgainstMime())) {
                return $detect;
            }

            if ($detect) {
                if ($this->_config['detect_prefer_mime']) {
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

        //if ($wellknown = array_search($encoding, array('HZ-GB-2312' => 'GB-2312'))) {
        //  return $wellknown;
        //}
        
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
        if (isset($array[1])) {
            $array = explode('=', $array[1]);
            if (isset($array[1])) {
                $charset = trim($array[1]);
                if (preg_match('/^UTF-?8$/i', $charset)) {
                    return 'UTF-8';
                } else {
                    return @mb_preferred_mime_name($charset);
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
