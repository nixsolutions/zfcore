<?php
/**
 * Mail_Form_Template_Edit
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Edit.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Mail_Form_Template_Edit extends Mail_Form_Template_Create
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
     * @return object Zend_Form_Element_Submit
     */
    protected function _submit()
    {
        return parent::_submit()->setLabel('Save');
    }
}