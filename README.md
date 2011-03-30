Diggin_Http_Response_Charset
============================

*offers converting charset AUTO and wrapping response object.*

This library aims to used in web-scraping.

Requirements
------------
  - PHP 5.2 or over
  - mbstring and iconv
  - Zend Framework 1.8 or over (optional)

Usage
-----
wrap response object:
    $client = new Zend_Http_Client($url);
    $response = $client->request();
    Diggin_Http_Response_Charset::wrapResponse($response);


Please see demos/Diggin/Http/Response/Charset .

Technical Information
---------------------
Diggin_Http_Response_Charset is based on HTMLScraping.

  - http://www.rcdtokyo.com/etc/htmlscraping/

License
-------
Diggin_Http_Response_Charset is licensed under LGPL(GNU Lesser General Public License).

Similar libray
--------------
- perl : HTTP::Response::Encoding
  - http://search.cpan.org/dist/HTTP-Response-Encoding/
  - http://blog.livedoor.jp/dankogai/archives/50811793.html
- python : Universal Encoding Detector
  - http://chardet.feedparser.org/
