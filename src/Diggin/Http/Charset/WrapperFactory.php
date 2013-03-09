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

final class WrapperFactory
{
    final private function __construct(){}

    /**
     * wrap response objects
     *  Zend\Http\Response, HttpMessage
     *
     *  @param mixed object to wrap
     *  @param string
     *  @return mixed
     */
    final public static function factory($response, $url = null)
    {
        if ($response instanceof \Zend\Http\Response) {
            $wrapper = new Wrapper\ZF2Wrapper();
            $wrapper->setVersion($response->getVersion());
            $wrapper->setStatusCode($response->getStatusCode());
            $wrapper->setReasonPhrase($response->getReasonPhrase());
            $wrapper->setHeaders($response->getHeaders());
            $wrapper->setContent($response->getContent());
            $wrapper->setMetadata($response->getMetadata());
            $wrapper->setUrl($url);

            return $wrapper;
        } elseif ($response instanceof \Symfony\Component\BrowserKit\Response) {
            $response = new Wrapper\Symfony2($response->getContent(),
                                             $response->getStatus(),
                                             $response->getHeaders());
            $response->setUrl($url);

            return $response;
        } elseif ($response instanceof \HttpMessage) {
            if (HTTP_MSG_RESPONSE !== $response->getType()) {
                throw new \Exception('Invalid Message Type :'. $response->getType());
            }

            $message = new Wrapper\PeclHttpMessageWrapper;
            $message->setType($response->getType());
            $message->setBody($response->getBody());
            $message->setHeaders($response->getHeaders());
            $message->setHttpVersion($response->getHttpVersion());
            $message->setResponseCode($response->getResponseCode());
            $message->setResponseStatus($response->getResponseStatus());

            $message->setUrl($url);

            return $message;
        } else {
            throw new Exception\UnexpectedValueException('Unknown Object Type..');
        }
    }

}
