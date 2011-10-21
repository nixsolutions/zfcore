<?php
/**
 * Register user form
 * 
 * @category Application
 * @package Debug
 * @subpackage Options
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */
class Options_Model_Options_Form_Edit extends Options_Model_Options_Form_Create
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        
        $this->setName('optionEditForm')
             ->addElement(new Zend_Form_Element_Hidden('id')); 
                
        return $this;
    }


    /**
     * Set values for EditForm

     * @param $values
     * @return void
     */
    public function setValues($values)
    {
        $this->getElement('namespace')
             ->setValue($values['namespace']);

        $this->getElement('name')
             ->setValue($values['name']);

        $this->getElement('value')
             ->setValue($values['value']);

        $this->getElement('type')
             ->setValue($values['type']);

    }
    
    /**
     * Modify parent element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _submit()
    {
        return parent::_submit()->setLabel('Save');
    }
}
