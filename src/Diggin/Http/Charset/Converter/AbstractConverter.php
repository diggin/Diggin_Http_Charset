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
namespace Diggin\Http\Charset\Converter;
use Diggin\Http\Charset\Converter;

abstract class AbstractConverter implements Converter
{
    /**
     * Return converted Response'body
     *
     * @param string       $body
     * @param string|array $metadata 'content-type'
     * $param $remains
     *
     * @return mixed
     */
    final public function convert($body, $metadata = array(), $remains = null)
    {
        if (is_string($metadata)) {
            $ctype = $metadata;
        } elseif (is_array($metadata)) {
            $ctype = isset($metadata['content-type']) ? $metadata['content-type'] : null;
        } else {
            $ctype = null;
        }

        $body = $this->_initBody($body);
        $encoding_from = $this->_encodingFrom($body, $ctype);

        // if not avilable for mbstring, using iconv
        if (!in_array($encoding_from, mb_list_encodings())) {
            $body = @iconv($encoding_from, 'UTF-8', $body);
            if (isset($remains)) {
                foreach ($remains as $k => $v) {
                    $remains[$k] = @iconv($encoding_from, 'UTF-8', $v);
                }

                return array($body, $remains);
            }

            return $body;
        }

        if (isset($remains)) {
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
        /**
         * Remove BOM & NULLs.
         */
        $body = preg_replace('/^\xef\xbb\xbf/', '' , $body);
        $body = str_replace("\x0", '', $body);

        return $body;
    }
}
