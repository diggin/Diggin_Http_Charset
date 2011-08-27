Diggin_Http_Charset
============================

*OFFERS detect & converting ALL response charset to UTF-8*
Most powerfull for MOJIBAKE.

(several charset - SJIS-win, TIS-620 and others..)

This library aims to used in web-scraping.

Requirements
------------
  - PHP 5.3 or over
  - mbstring and iconv

Usage
-----
wrap response object:
    use Diggin\Http\Charset\WrapperFactory;
    $client = new Zend_Http_Client($url);
    $response = $client->request();
    WrapperFactory::wrapResponse($response);

Please see demos/Diggin/Http/Charset .

Technical Information
---------------------
Diggin_Http_Charset is based on HTMLScraping.

  - http://www.rcdtokyo.com/etc/htmlscraping/

License
-------
Diggin_Http_Charset is licensed under LGPL(GNU Lesser General Public License).

Similar library
--------------
- perl : HTTP::Response::Encoding
  - http://search.cpan.org/dist/HTTP-Response-Encoding/
  - http://blog.livedoor.jp/dankogai/archives/50811793.html
- python : Universal Encoding Detector
  - http://chardet.feedparser.org/

FOR PHP5.2.x users
----------------
php5.2 branch is also available.
