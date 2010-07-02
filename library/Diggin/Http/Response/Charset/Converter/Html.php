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

require_once 'Diggin/Http/Response/Charset/Converter/ConverterAbstract.php';

class Diggin_Http_Response_Charset_Converter_Html
    extends Diggin_Http_Response_Charset_Converter_ConverterAbstract
{
    private $_detector;

    protected function _encodingFrom($body, $ctype)
    {
        return $this->getDetector()->detect($body, $ctype);
    }

    final public function getDetector()
    {
        if (!$this->_detector) {
            require_once 'Diggin/Http/Response/Charset/Detector/Html.php';
            $this->_detector = new Diggin_Http_Response_Charset_Detector_Html();
        }

        return $this->_detector;
    }
}

