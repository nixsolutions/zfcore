<?php
/**
 * ManagementController for admin module
 *
 * @category   Application
 * @package    Faq
 * @subpackage Controller
 */
class Faq_ManagementController extends Core_Controller_Action_Scaffold
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
     */
    public function indexAction()
    {

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
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    protected function _getCreateForm()
    {
        return new Faq_Model_Question_Form_Create();
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
        $form = new Faq_Model_Question_Form_Create();
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
        return new Faq_Model_Question_Table();
    }
}

