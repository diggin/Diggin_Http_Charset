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

require_once 'Diggin/Http/Response/Charset/Front/ConvertInterface.php';

class Diggin_Http_Response_Charset_Front_UrlRegex 
    implements Diggin_Http_Response_Charset_Front_ConvertInterface
{
    /**
     * @var Diggin_Http_Response_Charset_Converter_ConverterInterface
     */
    private $_defaultConverter;

    /**
     * @var array
     */
    protected $_converterSet = array();

    /**
     * Convert
     *
     * @param array $document
     * @param array $remains
     * @return mixed string|array
     */
    public function convert($document, $remains = null)
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

        if ($converter = $this->_checkMatch((string)$url)) {
            if (is_callable($converter)) {
                return call_user_func_array($converter, array($content, $remains));
            }
            if (is_string($converter)) {
                $converter = $this->_loadConverter($converter);
            }
            
            return $converter->convert($content, $remains);
        }

        return $this->getDefaultConverter()->convert($content, $remains);
    }

    /**
     * add Converter
     * $pattern should be preg-regex
     *
     * @param string $pattern
     * @param mixed string|Diggin_Http_Response_Charset_Converter_ConverterInterface
     * @return Diggin_Http_Response_Charset_Front_UrlRegex
     */
    public function addConverter($pattern, $converter)
    {
        if (!is_callable($converter) and
            !($converter instanceof Diggin_Http_Response_Charset_Converter_ConverterInterface) and
            !(in_array('Diggin_Http_Response_Charset_Front_ConvertInterface', class_implements($converter)) === false)) {
            require_once 'Diggin/Http/Response/Charset/Front/Exception.php';
            throw new Diggin_Http_Response_Charset_Front_Exception('Invalid Argument');
        }

        $this->_converterSet[$pattern] = $converter;

        return $this;
    }

    public function getConverterSet()
    {
        return $this->_converterSet;
    }

    protected function _checkMatch($url)
    {
        foreach ($this->getConverterSet() as $pattern => $converter) {
            if (preg_match($pattern, $url)) {
                return $converter;
            }
        }
    }

    protected function _loadConverter($converter)
    {
        if (!class_exists($converter)) {
            require_once 'Zend/Loader.php';
            $converter = Zend_Loader::loadClass($converter);
        }

        return new $converter;
    }

    final public function getDefaultConverter()
    {
        if (!$this->_defaultConverter) {
            require_once 'Diggin/Http/Response/Charset/Converter/Html.php';
            $this->_defaultConverter = new Diggin_Http_Response_Charset_Converter_Html;
        }

        return $this->_defaultConverter;
    }

}
