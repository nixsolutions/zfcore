<?php
/**
 * PagesController for Admin module
 *
 * @category   Application
 * @package    Pages
 * @subpackage Controller
 * 
 * @version  $Id: ManagementController.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Pages_ManagementController extends Core_Controller_Action_Scaffold
{
    /**
     * Init environmonet
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
     * Index action in Pages Controller
     *
     * @access public
     * @created Wed Aug 06 13:09:14 EEST 2008
     */
    public function indexAction()
    {
        // see view script
        // use dojox.grid.DataGrid
    }
    
    /**
     * createAction
     *
     * @return void
     */
    function createAction()
    {
        parent::createAction();
        $this->_setDefaultScriptPath();
    }
    
    /**
     * createAction
     *
     * @return void
     */
    function editAction()
    {
        parent::editAction();
        $this->_setDefaultScriptPath();
    }

    /**
     * _getManager
     *
     * return manager for scaffolding
     *
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Pages_Model_Page_Table();
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
        return new Pages_Model_Page_Form_Edit();
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
        $form =  new Pages_Model_Page_Form_Edit();
        $form->addElement(new Zend_Form_Element_Hidden('id'));
        
        return $form;
    }
}