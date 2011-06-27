<?php
/**
 * Categories_Form_Category_Create
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Create.php 206 2010-10-20 10:55:55Z AntonShevchuk $
 */
class Categories_Form_Category_Create extends Zend_Dojo_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('categoryForm')->setMethod('post');

        $this->addElements(
            array($this->_title(),
                 $this->_description(),
                 $this->_alias(),
                 $this->_parent(),
                 $this->_submit()
            )
        );
        return $this;
    }

    /**
     * Create mail subject element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _title()
    {
        $subject = new Zend_Dojo_Form_Element_ValidationTextBox('title');
        $subject->setLabel('Title')
                ->setRequired(true)
                ->setTrim(true)
                ->setAttribs(array('style'=>'width:60%'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        return $subject;
    }
    /**
     * Create mail subject element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _alias()
    {
        $subject = new Zend_Dojo_Form_Element_ValidationTextBox('alias');
        $subject->setLabel('Alias')
                ->setTrim(true)
                ->setAttribs(array('style'=>'width:60%'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(
                  'Db_NoRecordExists', false,
                  array(
                      array('table' => 'categories', 'field' => 'alias')
                  )
              );;
        return $subject;
    }

    /**
     * Create mail body element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _description()
    {
        $body = new Zend_Dojo_Form_Element_Editor('description');
        $body->setLabel('Description')
             ->setRequired(false)
             ->setAttribs(array('style'=>'width:60%'))
             ->addFilter('StringTrim');
        return $body;
    }

    /**
     * Create mail body element (text)
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _parent()
    {
        $element = new Zend_Dojo_Form_Element_FilteringSelect('parentId');
        $element->setLabel('Parent Category')
             ->setAttribs(array('style' => 'width:60%'))
             ->setRequired(false);

        $element->addMultiOption('', '');
        $categories = new Categories_Model_Categories_Table();
        $select = $categories->select()->order('path');
        foreach ($categories->fetchAll($select) as $row) {
            $element->addMultiOption($row->id, str_repeat("-", $row->level) . " " . $row->title);
        }
        return $element;
    }

    /**
     * Create submit element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _submit()
    {
        $submit = new Zend_Dojo_Form_Element_SubmitButton('submit');
        $submit->setLabel('Save');

        return $submit;
    }
}