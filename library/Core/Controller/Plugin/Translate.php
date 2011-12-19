<?php
/**
 * Core_Controller_Plugin_Translate
 *
 * @category   Core
 * @package    Core_Controller
 * @subpackage Plugins
 *
 * @version  $Id$
 */
class Core_Controller_Plugin_Translate extends Zend_Controller_Plugin_Abstract
{
    protected $_translate;

    public function __construct(Zend_Translate $translate)
    {
        $this->_translate = $translate;
    }
    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $adapter = $this->_translate->getAdapter();

        if (!$locale = $request->getParam('locale')) {
            $locale = $adapter->getLocale();
        } else {
            $adapter->setLocale($locale);
        }

        $router = Zend_Controller_Front::getInstance()->getRouter();
        $router->setGlobalParam('locale', $locale);
    }
}