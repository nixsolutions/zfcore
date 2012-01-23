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
    const OAUTH_TWITTER  = 'twitter';
    const OAUTH_FACEBOOK = 'facebook';
    const OAUTH_GOOGLE   = 'google';

    /**
     * Init controller plugins
     */
    public function init()
    {
        /* Initialize action controller here */
        parent::init();
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_manager = new Users_Model_Users_Manager();
    }

    /**
     * login page
     *
     * @return void
     */
    public function indexAction()
    {
        $form = new Users_Model_Users_Form_Login();
        $form->setAction($this->view->url(array(), 'login'));

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_getAllParams())) {
                if ($this->_manager->login($form->getValues())) {

                    /** redirect to previously */
                    $session = new Zend_Session_Namespace('Zend_Request');
                    if (isset($session->params)) {
                        $router = $this->getFrontController()->getRouter();
                        $url = $router->assemble($session->params, 'default', true);
                        $session->unsetAll();
                    }

                    if (empty($url) || strpos($url, 'login') !== false) {
                        $url = $this->getHelper('url')->url(
                            array(
                                'module' => 'users',
                                'controller' => 'index',
                                'action' => 'index'
                            ),
                            'default',
                            true
                        );
                    }

                    $this->_flashMessenger->addMessage('Now You\'re logged in');
                    $this->_redirect($url, array('prependBase' => false));
                } else {
                    // small brute force shield
                    sleep(1);
                    // TODO: failure: clear database row from session
                    $message = 'Authorization error. Please check login or/and password';
                }
            } else {
                // small brute force shield
                sleep(1);
                // failure: form
                $message = 'Authorization error. Please check login or/and password';
            }
            $this->view->messages = $message;
        }
        if (Zend_Registry::isRegistered('fbConfig')) {
            $this->view->facebook = true;
        }
        if (Zend_Registry::isRegistered('twitterConfig')) {
            $this->view->twitter = true;
        }
        if (Zend_Registry::isRegistered('googleConfig')) {
            $this->view->google = true;
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
            $this->_helper->redirector->gotoRoute(array(), 'login');
        }

        $reset = $this->_manager->clearHash($hash);
        if ($reset) {
            $message = 'Your password recovery request was cancelled.';
        } else {
            $message = 'Incorrect password recovery request.';
        }
        $this->_flashMessenger->addMessage($message);
        $this->_helper->redirector->gotoRoute(array(), 'login');
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
                $this->_helper->redirector->gotoRoute(array(), 'login');
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
        $this->_helper->redirector(false, false, false);
    }

    /**
     * Oauth Connect
     *
     */
    public function oauthAction()
    {
        $namespace = $this->_getOauthStorage();
        $info = $namespace->info;

        $users = new Users_Model_Users_Table();

        if (empty($info->email)) {
            $row = $users->getByTwid($info->twId);
        } else {
            $row = $users->getByEmail($info->email);
            if (!$row) {
                if (self::OAUTH_FACEBOOK == $this->_getParam('type')) {
                    $row = $users->getByFbuid($info->fbUid);
                } elseif (self::OAUTH_GOOGLE == $this->_getParam('type')) {
                    $row = $users->getByGid($info->gId);
                }
            }
        }
        if (!$row) {
            $loginFilter = new Zend_Filter_Alnum();
            $info->login = $loginFilter->filter($info->login);

            if ($users->getByLogin($info->login)) {

                $form = new Users_Form_Users_RegisterLogin();
                if ($this->getRequest()->isPost()
                    && $form->isValid($this->_getAllParams())) {

                    $info->login = $form->getValue('login');
                } else {
                    $this->view->login = $info->login;
                    $this->view->form = $form;
                    return;
                }
            }

            $row = $users->createRow($info->getArrayCopy());
            $row->role = Users_Model_User::ROLE_USER;
            $row->status = Users_Model_User::STATUS_ACTIVE;
            $row->save();
        }

        $row->login();
        $namespace->unsetAll();

        $this->_helper->flashMessenger->addMessage('Now You\'re Logging!');
        $this->_helper->redirector(false, false, false);
    }

    /**
     * Oauth Connect
     *
     * @return Zend_Session_Namespace
     */
    protected function _getOauthStorage()
    {
        $namespace = new Zend_Session_Namespace('oauth');

        if (empty($namespace->info)) {
            switch ($this->_getParam('type')) {
                case self::OAUTH_TWITTER:
                    $helper = $this->_helper->twitter;
                    break;
                case self::OAUTH_GOOGLE:
                    $helper = $this->_helper->google;
                    break;
                case self::OAUTH_FACEBOOK:
                    $helper = $this->_helper->facebook;
                    break;
            }
            $namespace->info = $helper->getInfo();
        }
        return $namespace;
    }
}