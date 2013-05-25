<?php
/**
 * Bootstrap Application
 *
 * @category Application
 * @package  Bootstrap
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initCliRouter()
    {
        if (PHP_SAPI === 'cli' && !defined('PHPUNIT')) {
            $this->bootstrap('frontController');
            // @var Zend_Controller_Front $frontController
            $frontController = $this->getResource('frontController');
            $frontController->setRouter(new Core_Controller_Router_Cli());
        }
    }

}
