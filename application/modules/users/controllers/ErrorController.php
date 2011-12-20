<?php
/**
 * ErrorController for default module
 *
 * @category   Application
 * @package    Users
 * @subpackage Controller
 *
 * @version  $Id: ErrorController.php 185 2010-08-09 14:14:47Z AntonShevchuk $
 */
class Users_ErrorController extends Core_Controller_Action
{
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
                $message = 'An unexpected error occurred with your request. '
                         . 'Please try again later.';
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