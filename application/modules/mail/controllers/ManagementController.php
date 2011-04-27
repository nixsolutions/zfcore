<?php
/**
 * MailController for admin module
 *
 * @category   Application
 * @package    Dashboard
 * @subpackage Controller
 *
 * @version  $Id: MailController.php 206 2010-10-20 10:55:55Z AntonShevchuk $
 */
class Mail_ManagementController extends Core_Controller_Action_Scaffold
{
    /**
     * init environment
     */
    public function init()
    {
        /* Initialize */
        parent::init();

        /* is Dashboard Controller */
        $this->_isDashboard();
    }

    /**
     * Index action
     * Show All
     *
     * @todo maybe create Datagrid config and put there all options
     * @see view
     */
    public function indexAction()
    {
        $this->view->aOptions = array('maxLength' => 140);
    }

    /**
     * Edit action
     */
    public function editAction()
    {
        parent::editAction();
    }

    /**
     * delete Action
     */
    public function deleteAction()
    {
        throw new Zend_Controller_Action_Exception('Page not found', 404);
    }

    /**
     * create action
     */
    public function createAction()
    {
        throw new Zend_Controller_Action_Exception('Page not found', 404);
    }

    /**
     * send action
     */
    public function sendAction()
    {
        $form = new Mail_Form_Template_Send();

        if ($this->_request->isPost()
            && $form->isValid($this->_getAllParams())) {

            try {
                $model = new Mail_Model_Templates_Model($form->getValues());
                $model->send();

                $this->_flashMessenger->addMessage('Mail Send');
                $this->_helper->getHelper('redirector')->direct('index');
            } catch (Exception $e) {
                return $this->_forward(
                    'internal',
                    'error',
                    'admin',
                    array('error' => $e->getMessage())
                );
            }
        }
        if ($alias = $this->_getParam('alias')) {
            if (!$defaults = $this->_getTable()->getByAlias($alias)) {
                return $this->_forward(
                    'internal',
                    'error',
                    'admin',
                    array('error' => "Not found such mail alias '{$alias}'")
                );
            }
            $form->setDefaults($defaults->toArray());
        }
        $this->view->mailForm = $form;
    }

    /**
     * _getTable
     *
     * return dbTable for scaffolding
     *
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Mail_Model_Templates_Table();
    }

    /**
     * disable create from
     */
    protected function _getCreateForm()
    {
        return;
    }

    /**
     * Get mail edit form
     *
     * @return object Model_Form_Admin_Mail_Edit
     */
    protected function _getEditForm()
    {
        return new Mail_Form_Template_Edit();
    }
}

