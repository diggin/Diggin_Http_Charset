<?php
namespace Diggin\Http\Charset\Wrapper\ZF2Wrapper;
use Diggin\Http\Charset\Filter;
use Zend\Http\Header\ContentType;

class HeaderContentType extends ContentType
{

    public function getOriginalFieldValue()
    {
        return parent::getFieldValue();
    }

    public function getFieldValue()
    {
        return Filter::replaceHeaderCharset($this->value);
    }

    public function toString()
    {
        return 'Content-Type: ' . $this->getFieldValue();
    }
}
