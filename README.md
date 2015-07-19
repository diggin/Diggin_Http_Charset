Diggin_Http_Charset
============================

Automatically convert to UTF-8.

Master: [![Build Status](https://travis-ci.org/diggin/Diggin_Http_Charset.png?branch=master)](https://travis-ci.org/diggin/Diggin_Http_Charset)
[![Coverage Status](https://coveralls.io/repos/diggin/Diggin_Http_Charset/badge.png)](https://coveralls.io/r/diggin/Diggin_Http_Charset)

Detecting based on header's charset & html meta charset.

(handling several charset more carefully  - SJIS-win, TIS-620 and others..)

This library aims to used in web-scraping.

Requirements
------------
  - PHP 5.3 or over
  - mbstring and iconv

Usage
-----
1. wrap response object:

```php
<?php
use Diggin\Http\Charset\WrapperFactory;
$client = new Zend\Http\Client($url);
$response = $client->send();
$response = WrapperFactory::factory($response); // then, response getBody() return with converted UTF-8.
```

Please see more at demos/Diggin/Http/Charset .

Guzzle & Goutte
---------------
[guzzle-plugin-AutoCharsetEncodingPlugin](https://github.com/diggin/guzzle-plugin-AutoCharsetEncodingPlugin) supports for using with Guzzle3.

[Usage of with Behat](https://gist.github.com/MugeSo/3971150) by @MugeSo

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

TODOs
-------------
- handling non text/html content types.
- better APIs & according ZF2 coding standard.
- struggle in more charset :-\

