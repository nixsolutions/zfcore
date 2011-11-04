<?php
/**
 * Application_View_Helper_Version
 *
 * get version
 */
class Application_View_Helper_Version extends Zend_View_Helper_Abstract
{
    /**
     * cache
     *
     * @var string
     */
    static public $_cache = null;

    /**
     * get version
     *
     * @return string
     */
    public function version()
    {
        if (null === self::$_cache) {
            self::$_cache = trim(`hg parent | grep tag: | awk '{print($2)}'`);
        }
        return self::$_cache;
    }
}
