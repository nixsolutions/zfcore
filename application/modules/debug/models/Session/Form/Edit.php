<?php
/**
 * Register session form
 * 
 * @category Application
 * @package Session
 * @subpackage Form
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */
class Debug_Model_Session_Form_Edit extends Debug_Model_Session_Form_Create
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        
        $this->setName('sessionEditForm')
             ->addElement(new Zend_Form_Element_Hidden('id')); 
                
        return $this;
    }
    
    /**
     * Modify parent element
     *
     * 
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _submit()
    {
        return parent::_submit()->setLabel('Save');
    }
}