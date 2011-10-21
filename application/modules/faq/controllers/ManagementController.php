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
     * init controller
     *
     * @return void
     */
    public function init()
    {
        /* Initialize */
        parent::init();

        /** init grid */
        $this->_beforeGridFilter(array(
             '_addAllTableColumns',
             '_prepareGrid',
             '_addEditColumn',
             '_addDeleteColumn'
        ));

    }

    /**
     * _getCreateForm
     *
     * return create form for crud
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
     * return edit form for crud
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
     * return manager for crud
     *
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Faq_Model_Question_Table();
    }

    /**
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->grid
             ->setColumn('question', array(
               'formatter' => array($this, array('trimFormatter'))
             ))
             ->setColumn('answer', array(
               'formatter' => array($this, array('trimFormatter'))
             ));
    }
}

