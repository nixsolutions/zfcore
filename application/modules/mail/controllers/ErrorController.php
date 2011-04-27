<?php
/**
 * ErrorController for admin module
 *
 * @category   Application
 * @package    Dashboard
 * @subpackage Controller
 * 
 * @version  $Id: ErrorController.php 144 2010-07-05 13:50:23Z AntonShevchuk $
 */
class Admin_ErrorController extends Core_Controller_Action
{
    /**
     * Flash Messenger
     *
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $_flashMessenger;
    
    /**
     * Init controller plugins
     *
     */
    public function init()
    {
        /* Initialize */
        parent::init();
        
        /* is Dashboard Controller */
        $this->_isDashboard();
              
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    /**
     * errorAction
     *
     * @access public
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $message = "The page you requested was not found.";
                break;
            default:
            // application error
                $this->getResponse()->setHttpResponseCode(500);
                $message = 'An unexpected error occurred with your request. ' .
                           'Please try again later.';
                break;
        }
        $this->view->message   = $message;
        $this->view->debug     = Zend_Registry::getInstance()->config
                                                             ->application
                                                             ->debug;
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }

    /**
     * internalAction
     *
     * @access public
     */
    public function internalAction()
    {
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->message = $this->_getParam('error');
    }
    
    /**
     * deniedAction
     *
     * @access public
     */
    public function deniedAction()
    {
        $this->_helper->layout->setLayout('dashboard/denied');
    }

    /**
     * notfoundAction
     *
     * @access public
     */
    public function notfoundAction()
    {
        $this->getResponse()->setHttpResponseCode(404);
        $this->view->request = $this->getRequest();
    }
}