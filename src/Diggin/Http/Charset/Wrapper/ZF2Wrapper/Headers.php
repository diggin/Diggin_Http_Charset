<?php
namespace Diggin\Http\Charset\Wrapper\ZF2Wrapper;
use Zend\Http\Headers as BaseHeaders;
use Zend\Http\HeaderLoader;

class Headers extends BaseHeaders
{
    public function getPluginClassLoader()
    {
        if ($this->pluginClassLoader == null) {
            $headerLoader = new HeaderLoader;
            $headerLoader->registerPlugin('contenttype', 'Diggin\Http\Charset\Wrapper\ZF2Wrapper\HeaderContentType');
            $this->pluginClassLoader = $headerLoader;
        }

        return $this->pluginClassLoader;
    }
}
