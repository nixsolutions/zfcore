<?php
/**
 * ManagementController for admin module
 *
 * @category   Application
 * @package    Faq
 * @subpackage Controller
 */
class Faq_ManagementController extends Core_Controller_Action_Crud
{
    /**
     * _getCreateForm
     *
     * return create form for crud
     *
     * @return  Faq_Model_Question_Form_Create
     */
    protected function _getCreateForm()
    {
        return new Faq_Model_Question_Form_Create();
    }

    /**
     * _getEditForm
     *
     * return edit form for crud
     *
     * @return  Zend_Form
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
     * return manager for crud
     *
     * @return  Faq_Model_Question_Table
     */
    protected function _getTable()
    {
        return new Faq_Model_Question_Table();
    }


    /**
     * _perpareHeader
     *
     * @return void
     */
    function _prepareHeader()
    {
        $this->_addCreateButton();
        $this->_addDeleteButton();
    }

    /**
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->_addCheckBoxColumn();
        $this->_grid
             ->setColumn(
                 'question',
                 array(
                     'name'  => 'Question',
                     'type'  => Core_Grid::TYPE_DATA,
                     'index' => 'question',
                     'formatter' => array($this, array('trimFormatter'))
                 )
             );
        $this->_addEditColumn();
        $this->_addDeleteColumn();
    }
}

