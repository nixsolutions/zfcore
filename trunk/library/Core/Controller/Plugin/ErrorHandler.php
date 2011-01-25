<?php
/**
 * Controller plugin that sets the correct paths
 * to the Zend_Controller_Plugin_errorHandler instances
 *
 * @uses       Zend_Controller_Plugin_Abstract
 * 
 * @category   Core
 * @package    Core_Controller
 * @subpackage Plugins
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * 
 * @version  $Id: ErrorHandler.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Core_Controller_Plugin_ErrorHandler 
    extends Zend_Controller_Plugin_Abstract
{
    /**
     * dispatchLoopStartup
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // Configure the error plugin to use the loaded module
        // so we can use module-specific error handling
        $frontController = Zend_Controller_Front::getInstance();
        $errorPlugin = $frontController->getPlugin('Zend_Controller_Plugin_ErrorHandler');
        $errorPlugin->setErrorHandlerModule($request->getModuleName());
    }
}