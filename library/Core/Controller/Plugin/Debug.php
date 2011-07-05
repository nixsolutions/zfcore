<?php
/**
 * Front Controller Plugin
 * 
 * @uses       Zend_Controller_Plugin_Abstract
 * 
 * @category   Core
 * @package    Core_Controller
 * @subpackage Plugins
 * 
 * @version  $Id: Debug.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Core_Controller_Plugin_Debug extends Zend_Controller_Plugin_Abstract
{
    /**
     * Constructor
     *
     * Options may include:
     * - config
     *
     * @param  Array $options
     */
    public function __construct(Array $options = array())
    {
        Core_Debug::setEnabled(true);
        Core_Debug::getGenerateTime('init');
    }

    /**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        Core_Debug::getGenerateTime("routeStartup() called");
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
        Core_Debug::getGenerateTime("routeShutdown() called");
    }
    
    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        Core_Debug::getGenerateTime("dispatchLoopStartup() called");
    }
    
    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() 
     * setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        Core_Debug::getGenerateTime("preDispatch() called");
    }
    
    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior. By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() 
     * setDispatched(false)}),
     * a new action may be specified for dispatching.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        Core_Debug::getGenerateTime("postDispatch() called");
    }
    
    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        Core_Debug::getGenerateTime("dispatchLoopShutdown() called");
    }
}