<?php
/**
 * Register user form
 * 
 * @category Application
 * @package Model
 * @subpackage Form
 * 
 * @version  $Id: Layout.php 160 2010-07-12 10:47:54Z AntonShevchuk $
 */
class Model_Mail_Form_Layout extends Model_Mail_Form_Create
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('mailLayoutForm')
             ->setMethod('post');
        
        $this->addElements(array($this->_body(), $this->_submit()));
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