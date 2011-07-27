<?php
/**
 * Users_ProfileController
 *
 * @category   Application
 * @package    Users
 * @subpackage Controller
 * @todo For login form http://designreviver.com/inspiration/100-sites-with-outstanding-login-forms/
 *
 * @version  $Id: LoginController.php 170 2010-07-26 10:56:18Z AntonShevchuk $
 */
class Users_ProfileController extends Core_Controller_Action
{
    /**
     * My profile
     */
    public function indexAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();

        $this->_forward('view', null, null, array('id' => $identity->id));
    }

    /**
     *
     */
    public function viewAction()
    {
        if (!$id = $this->_getParam('id')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $users = new Users_Model_Users_Table();
        if (!$row = $users->getById($id)) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $this->view->row = $row;
    }

    /**
     * The default action - show the home page
     */
    public function editAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $users = new Users_Model_Users_Table();
        $row = $users->getById($identity->id);

        $form = new Users_Model_Users_Form_Profile();
        $form->setUser($row);

        if ($this->_request->isPost()
            && $form->isValid($this->_getAllParams())) {

            $row->setFromArray($form->getValues());
            $row->save();

            $row->login();

            $this->_helper->flashMessenger('Profile Updated');
            $this->_helper->redirector('index');
        }
        $this->view->form = $form;
    }
}