<?php
/**
 * Register user form
 * 
 * @category Application
 * @package Model
 * @subpackage Form
 * 
 * @version  $Id: Edit.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Model_Mail_Form_Edit extends Model_Mail_Form_Create
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        return parent::init()->setName('mailEditForm')
                             ->addElement(new Zend_Form_Element_Hidden('id'));
    }
    
    /**
     * Create submit element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _submit()
    {
        return parent::_submit()->setLabel('Save');
    }
}