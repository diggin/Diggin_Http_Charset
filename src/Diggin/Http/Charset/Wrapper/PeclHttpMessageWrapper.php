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
namespace Diggin\Http\Charset\Wrapper;
use Diggin\Http\Charset\Front\DocumentConverter;

class PeclHttpMessageWrapper extends \HttpMessage
{
    /**
     * @var Diggin\Http\Charset\Front\DocumentConverter
     */
    private $_charsetfront;

    private $_url;

    public function setCharsetFront(DocumentConverter $charsetfront)
    {
        $this->_charsetfront = $charsetfront;
    }

    public function getCharsetFront()
    {
        if (!$this->_charsetfront) {
            $this->_charsetfront = new \Diggin\Http\Charset\Front\UrlRegex;
        }

        return $this->_charsetfront;
    }

    public function getBody()
    {
        $content = array('body' => parent::getBody(), 'content-type' => $this->getHeader('content-type'));
        $document = array('url' => $this->getUrl(), 'content' => $content);

        return $this->getCharsetFront()->convert($document);
    }

    public function setUrl($url)
    {
        $this->_url = $url;
    }

    public function getUrl()
    {
        return $this->_url;
    }

}
