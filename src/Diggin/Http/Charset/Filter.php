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
namespace Diggin\Http\Charset;

final class Filter
{
    final private function __construct(){}

    /**
     * Cut original Header's Charset
     * note: Zend\Http\Response's Headers key is ucwords(strtolower) & underscores
     *
     * @param  array $headers (supported only Zend\Http\Response's Headers)
     * @return array
     */
    final public static function clearHeadersCharset($headers)
    {
        if (isset($headers['Content-Type'])) {
            $headers['Content-Type'] = self::clearHeaderCharset($headers['Content-Type']);
        }

        return $headers;
    }

    final public static function clearHeaderCharset($header)
    {
        return trim(preg_replace('/;\scharset=[A-Za-z0-9-_]+/i', '', $header));
    }

    final public static function replaceHeadersCharset($headers, $charset = 'UTF-8')
    {
        if (isset($headers['Content-Type'])) {
            $headers['Content-Type'] = self::replaceHeaderCharset($headers['Content-Type'], $charset);
        }

        return $headers;
    }

    final public static function replaceHeaderCharset($header, $charset = 'UTF-8')
    {
        list($main) = explode(';', $header, 2);

        return "$main; charset=$charset;";
    }
}
