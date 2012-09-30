<?php

/**
 * Diggin_Http_Charset
 *
 * a part of this package (Diggin_Http_Charset_Detector_Html) is
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

/**
 * @namespace
 */
namespace Diggin\Http\Charset\Front;
use Diggin\Http\Charset\Converter\HtmlConverter;
use Diggin\Http\Charset\Converter\Exception;

class UrlRegex implements DocumentConverter
{
    /**
     * @var Diggin\Http\Charset\Converter
     */
    private $_defaultConverter;

    /**
     * @var array
     */
    protected $_converterSet = array();

    /**
     * Convert Content(string, HTML&url set)
     *
     * @param string $body
     * @param string $metadata
     *  expected
     *   case 1 metadata array :
     *          ['content-type' => $var, 'url' => $url]
     *   case 2 url as string :
     *          'http://..'
     * @param  array $remains
     * @return mixed string|array
     */
    public function convert($body, $metadata = array(), $remains = null)
    {
        $url = false;
        if (is_array($metadata) && isset($metadata['url'])) {
            $url = $metadata['url'];
            unset($metadata['url']);
        } elseif (is_string($metadata)) {
            $url = $metadata;
        }

        if ($url && $converter = $this->_checkMatch((string) $url)) {
            if (is_callable($converter)) {
                return call_user_func_array($converter, array($body, $metadata, $remains));
            }
            if (is_string($converter)) {
                $converter = $this->_loadConverter($converter);
            }

            return $converter->convert($body, $metadata, $remains);
        }

        return $this->getDefaultConverter()->convert($body, $metadata, $remains);
    }

    /**
     * add Converter
     * $pattern should be preg-regex
     *
     * @param string $pattern
     * @param mixed string|Diggin_Http_Charset_Converter_ConverterInterface
     * @return Diggin_Http_Charset_Front_UrlRegex
     */
    public function addConverter($pattern, $converter)
    {
        if (!is_callable($converter) and
            !($converter instanceof \Diggin\Http\Charset\Converter)) {
            throw new Exception\InvalidArgumentException('Argument converter IS NOT callable and Converter instance.');
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
            throw new Exception\RuntimeException('class unload');
        }

        return new $converter;
    }

    final public function getDefaultConverter()
    {
        if (!$this->_defaultConverter) {
            $this->_defaultConverter = new HtmlConverter;
        }

        return $this->_defaultConverter;
    }

}
