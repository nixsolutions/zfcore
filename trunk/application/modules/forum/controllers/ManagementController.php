<?php
/**
 * UsersController for admin module
 *
 * @category   Application
 * @package    Users
 * @subpackage Controller
 * 
 * @version  $Id: ManagementController.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Forum_ManagementController extends Core_Controller_Action_Scaffold
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

    }

    /**
     * createAction
     *
     * @return void
     */
    public function createAction()
    {
        parent::createAction();
        $this->_setDefaultScriptPath();
    }

    /**
     * editAction
     *
     * @return void
     */
    public function editAction()
    {
        parent::editAction();
        $this->_setDefaultScriptPath();
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
        return new Forum_Model_Post_Form_Admin_Create();
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
        $form = new Forum_Model_Post_Form_Admin_Create();
        $form->addElement(new Zend_Form_Element_Hidden('id'));
        return $form;
    }

    /**
     * _getTable
     *
     * return manager for scaffolding
     *
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Forum_Model_Post_Table();
    }
}

