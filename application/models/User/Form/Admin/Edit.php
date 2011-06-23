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
class Model_User_Form_Admin_Edit extends Users_Model_Users_Form_Admin_Create
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        
        $this->setName('userEditForm')
             ->addElement(new Zend_Form_Element_Hidden('id')); 
                
        return $this;
    }
    
    /**
     * Modify parent element
     *
     * @param string $aLabel
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _submit()
    {
        return parent::_submit()->setLabel('Save');
    }
    
    /**
     * Modify parent element
     *
     * @param string $aLabel
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _login()
    {
        return parent::_login()->removeValidator('Db_NoRecordExists');
    }
    
    /**
     * Modify parent element
     *
     * @param string $aLabel
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _email()
    {
        return parent::_email()->removeValidator('Db_NoRecordExists');
    }
}