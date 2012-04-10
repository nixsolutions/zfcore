<?php
/**
 * Edit page form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Pages_Form_Edit extends Pages_Form_Create
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->addElement(new Zend_Form_Element_Hidden('id'));

        $this->getElement('submit')
             ->setLabel('Save');
    }
}
