<?php
/**
 * Application_View_Helper_Version
 *
 * get version
 */
class Application_View_Helper_Version extends Zend_View_Helper_Abstract
{
    /**
     * get version
     *
     * @return string
     */
    public function version()
    {
        return Core_Version::getVersion();
    }
}
