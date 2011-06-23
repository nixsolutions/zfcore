<?php
/**
 * UsersController for admin module
 *
 * @category   Application
 * @package    Users
 * @subpackage Controller
 * 
 * @version  $Id: ManagementController.php 124 2010-04-21 16:57:01Z AntonShevchuk $
 */
class Users_ManagementController extends Core_Controller_Action_Scaffold
{
    /**
     * init invironment
     *
     * @return void
     */
    public function init()
    {
        /* Initialize */
        parent::init();
        
        /* is Dashboard Controller */
        $this->_isDashboard();
    }

    /**
     * indexAction
     *
     */
    public function indexAction()
    {
        $this->view->aStatus = array(Users_Model_User::STATUS_ACTIVE, 
                                     Users_Model_User::STATUS_BLOCKED, 
                                     Users_Model_User::STATUS_REGISTER, 
                                     Users_Model_User::STATUS_REMOVED);
    }

    /**
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    protected function _getCreateForm()
    {
        return new Users_Model_Users_Form_Admin_Create();
    }
    
    /**
     * _getEditForm
     *
     * return edit form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    protected function _getEditForm()
    {
        return new Users_Model_Users_Form_Admin_Edit();
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
        return new Users_Model_Users_Table();
    }
}

