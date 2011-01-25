<?php
/**
 * ErrorController for default module
 *
 * @category   Application
 * @package    Default
 * @subpackage Controller
 * 
 * @version  $Id: ErrorController.php 185 2010-08-09 14:14:47Z AntonShevchuk $
 */
class ErrorController extends Core_Controller_Action
{
    /**
     * Init controller plugins
     *
     */
    public function init()
    {
        /* Initialize action controller here */        
        parent::init();
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
                $message = $this->__("The page you requested was not found.");
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $message = $this->__(
                    'An unexpected error occurred with your request. ' .
                    'Please try again later.'
                );
                break;
        }
        $this->view->message   = $message;
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
        
    }
    
    /**
     * notfoundAction
     *
     * @access public
     */
    public function notfoundAction()
    {
        $this->getResponse()->setHttpResponseCode(404);
    }
}