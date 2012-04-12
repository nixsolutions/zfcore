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


    /**
     * custom grid filters
     *
     * @return void
     */
    protected function _prepareHeader()
    {
        $this->_addCreateButton();
        $this->_addDeleteButton();
    }

    /**
     * Prepare grid - remove not needed columns
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->_addCheckBoxColumn();

        $this->_addAllTableColumns();

        $this->_addEditColumn();
        $this->_addDeleteColumn();
    }
}