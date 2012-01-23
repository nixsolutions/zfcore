<?php
/**
 * ManagementController for module
 *
 * @category   Application
 * @package    Categories
 * @subpackage Controller
 */
class Categories_ManagementController extends Core_Controller_Action_Crud
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

        $this->_beforeGridFilter(
            array(
                '_addCheckBoxColumn',
                '_addAllTableColumns',
                //'_prepareGrid',
                '_addEditColumn',
                '_addDeleteColumn',
                '_addCreateButton',
                '_addDeleteButton',
                '_showFilter'
            )
        );

    }

    /**
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Form
     */
    protected function _getCreateForm()
    {
        return new Categories_Form_Category_Create();
    }

    /**
     * _getEditForm
     *
     * return edit form for scaffolding
     *
     * @return  Zend_Form
     */
    protected function _getEditForm()
    {
        $form = new Categories_Form_Category_Edit();
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
        return new Categories_Model_Category_Table();
    }
}