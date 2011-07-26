<?php
/**
 * LoginController for users module
 *
 * @category   Application
 * @package    Users
 * @subpackage Controller
 * @todo For login form http://designreviver.com/inspiration/100-sites-with-outstanding-login-forms/
 *
 * @version  $Id: LoginController.php 170 2010-07-26 10:56:18Z AntonShevchuk $
 */
class Users_LoginController extends Core_Controller_Action
{

    /**
     * Init controller plugins
     *
     */
    public function init()
    {
        /* Initialize action controller here */
        parent::init();
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_manager = new Users_Model_Users_Manager();
    }

    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        $form = new Users_Model_Users_Form_Login();

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_getAllParams())) {
                if ($this->_manager->login($form->getValues())) {

                    $this->_flashMessenger->addMessage('Now You\'re Logging!');

                    $session = new Zend_Session_Namespace('Zend_Request');
                    if (isset($session->params)) {
                        // redirect to previously
                        $router = $this->getFrontController()->getRouter();
                        $url    = $router->assemble(
                            $session->params,
                            'default',
                            true
                        );
                        $session->unsetAll();
                        if (strpos($url, 'login') !== false) {
                            $url = "/";
                        }
                    }
                    $this->_redirect(isset($url)?$url:'/');
                } else {
                    // small bruteforce shield
                    sleep(1);
                    // TODO: failure: clear database row from session
                    $message = 'Authorization error. '.
                        'Please check login or/and password';
                }
            } else {
                // small bruteforce shield
                sleep(1);
                // failure: form
                $message = 'Authorization error. '
                         . 'Please check login or/and password';
            }
            $this->view->messages = $message;
        }
        $this->view->form = $form;
    }

    /**
     * Cancel recovery password
     */
    public function cancelPasswordRecoveryAction()
    {
        $hash = $this->_getParam('hash');
        if (!$hash || !$this->_manager->isSetUserHash($hash)) {

            $this->_helper->flashMessenger
                 ->addMessage('Incorect request recover password');
            $this->_redirect('/login');
        }

        $reset = $this->_manager->clearHash($hash);
        if ($reset) {
            $message = 'Your password recovery request was cancelled.';
        } else {
            $message = 'Incorrect password recovery request.';
        }
        $this->_flashMessenger->addMessage($message);
        $this->_redirect('/login');
    }

    /**
     * Change password
     */
    public function recoverPasswordAction()
    {
        $hash = $this->_getParam('hash');
        $form = new Users_Model_Users_Form_NewPassword();

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_getAllParams())) {
                $password = $this->_getParam('passw');
                $result = $this->_manager->setPassword($hash, $password);
                if ($result) {
                    $message = 'You have changed your password.';
                } else {
                    $message = 'Incorrect password recovery request.';
                }
                $this->_flashMessenger->addMessage($message);
                $this->_redirect('/login');
            } else {
                $message = array_merge(
                    $form->getMessages('passw'),
                    $form->getMessages('passw_again')
                );
                $this->view->messages = $message;
            }
        }
        $this->view->form = $form;
    }

    /**
     * this action destroys all elements stored in the user's session
     * and redirects back to homepage
     */
    public function logoutAction()
    {
        $this->_manager->logout();
        $this->_helper->flashMessenger->addMessage('Logout successfull');
        $this->_redirect('/');
    }

    /**
     * Oauth Connect
     *
     */
    public function oauthAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        switch ($this->_getParam('type')) {
            case 'twitter':
                $this->_helper->twitter->login();
                break;
            case 'google':
                $this->_helper->google->login();
                break;
            case 'facebook':
                $this->_helper->facebook->login();
                break;
        }
        $this->_helper->flashMessenger->addMessage('Now You\'re Logging!');

        $this->_redirect('/');
    }
}