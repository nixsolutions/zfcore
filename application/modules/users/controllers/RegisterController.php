<?php
/**
 * RegisterController for default module
 *
 * @category   Application
 * @package    Users
 * @subpackage Controller
 * 
 * @version  $Id: RegisterController.php 170 2010-07-26 10:56:18Z AntonShevchuk $
 */
class Users_RegisterController extends Core_Controller_Action
{
    /**
     * Init controller plugins
     */
    public function init()
    {
        parent::init();
        /* Initialize action controller here */
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        
        /* Initialize user model */
        $this->_manager = new Users_Model_Users_Manager();
    }

    /**
     * The default action - register page
     */
    public function indexAction()
    {
        $form = new Users_Model_Users_Form_Register();
       
        if ($this->_request->isPost()) {

            if ($form->isValid($this->_getAllParams()) ) {
                if ($user = $this->_manager->register($form->getValues())) {
                    // confirm email sends to user
                    Mail_Model_Mail::register($user);
                    
                    $message = $this->__(
                        'Now you\'re registered! Please ' .
                        'check your email and confirm your registration'
                    );
                    $this->_flashMessenger->addMessage($message);
                    
                    $this->_redirect('/');
                } else {
                    $message = $this->__(
                        'Something goes wrong. ' .
                        'Please fill the registration form again'
                    );
                }
            } else {
                $message = $this->__(
                    'Registration error. '.
                    'Please check the form fields'
                );
            }
        }
        $this->view->messages = $message;
        $this->view->form = $form;
    }

    /**
     * Confirms registration with hash
     */
    public function confirmRegistrationAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();
        
        if ($this->_request->isGet() && 
            $hash = $this->_getParam('hash')) {
            if ($this->_manager->confirmRegistration($hash)) {

                $message = $this->__(
                    'Now you\'re confirm your registration! ' .
                    'Please log in now'
                );
                $this->_flashMessenger->addMessage($message);
            
                $this->_redirect('/login');
            } else {
                $message = $this->__(
                    'The user with specified data not found! '.
                    'Possibly you\'re already confirmed your registration'
                );
                    
                $this->_flashMessenger->addMessage($message);
            }
        }
        $this->_redirect('/');
    }

    /**
     * Forget password action
     *
     * 2-steps password restore:
     *
     * 1: confirm on password restoration sents to users' email
     * 2: if confirmed, new password generated and sends to user email
     * (forgetPwdConfirmAction)
     */
    public function forgetPasswordAction()
    {
        $form = new Users_Model_Users_Form_Forget();
        
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_getAllParams())) {
                $user = $this->_manager
                             ->forgetPassword($form->getValue('email'));
                if ($user) {
                    // send email
                    Mail_Model_Mail::forgetPassword($user);
                    
                    $message = $this->__(
                        'The confirmation email to reset your ' .
                        'password is sent. Please check your email '
                    );
                               
                    $this->_flashMessenger->addMessage($message);

                    $this->_redirect('/login');
                }
            }
            $message = $message = $this->__('Your email is not registered');
            $this->_flashMessenger->addMessage($message);
        }
        $this->view->form = $form;
    }


    /**
     * Checks the password confirmation
     */
    public function forgetPasswordConfirmAction()
    {
        if ($this->_request->isGet()) { 
            if ($hash = $this->_getParam('hash')) {
                
                $password = null;
                if ($this->_getParam('confirmReset') == 'yes') {
                    $password = $this->_manager->generatePassword();
                }
                
                $result = $this->_manager
                               ->forgetPasswordConfirm($hash, $password);
                
                if ($result === true) {
                    $message = $this->__('Your password reset request was cancelled');
                    $this->_flashMessenger->addMessage($message);
                    $this->_redirect('/login');
                } elseif ($result) {
                    // confirm email sends to user
                    Mail_Model_Mail::newPassword($result, $password);

                    $message = $this->__('You got new password. Please check your email');
                    $this->_flashMessenger->addMessage($message);
                    $this->_redirect('/login');
                } else {
                    //$user is found
                    $message = $this->__(
                        'The user with specified data not found! ' .
                        'Possibly you\'re already confirmed your reset '  .
                        'password data'
                    );
                    $this->_flashMessenger->addMessage($message);
                    $this->_redirect('/');
                }
            } 
        }
        $this->_redirect('/');
    }

}