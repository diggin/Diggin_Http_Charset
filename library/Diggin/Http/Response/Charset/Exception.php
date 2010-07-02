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

if (!$fh = @fopen('Diggin/Http/Response/Exception.php', 'r', true)) {
    class Diggin_Exception extends Exception{}
    class Diggin_Http_Exception extends Diggin_Exception{}
    class Diggin_Http_Response_Exception extends Diggin_Http_Exception{}
    class Diggin_Http_Response_Charset_Exception extends Diggin_Http_Response_Exception
    {}
} else {
    @fclose($fh);
    /**
     * @see Diggin_Http_Response_Exception
     */
    require_once 'Diggin/Http/Response/Exception.php';
    class Diggin_Http_Response_Charset_Exception extends Diggin_Http_Response_Exception
    {}
}

