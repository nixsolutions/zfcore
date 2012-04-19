<?php
/**
 * Categories_Form_Category_Create
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Categories_Form_Category_Create extends Core_Form
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
            array(
                 $this->_title(),
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
     * @return object Zend_Form_Element_Text
     */
    protected function _title()
    {
        $subject = new Zend_Form_Element_Text('title');
        $subject->setLabel('Title')
                ->setRequired(true)
//                ->setTrim(true)
                ->setAttribs(array('class'=>'span6'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        return $subject;
    }
    /**
     * Create mail subject element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _alias()
    {
        $subject = new Zend_Form_Element_Text('alias');
        $subject->setLabel('Alias')
                ->setAttribs(array('class'=>'span4'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(
                    'Db_NoRecordExists',
                    false,
                    array(
                        array('table' => 'categories', 'field' => 'alias')
                    )
                );
        return $subject;
    }

    /**
     * Create mail body element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _description()
    {
        $description = new Core_Form_Element_Redactor('description', array(
           'label' => 'Description',
           'cols'  => 50,
           'rows'  => 5,
           'required' => true,
           'filters' => array('StringTrim'),
           'redactor' => array(
               'imageUpload'  => false,
               'fileUpload'   => false,
           )
        ));
        return $description;
    }

    /**
     * Create mail body element (text)
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _parent()
    {
        $element = new Zend_Form_Element_Select('parentId');
        $element->setLabel('Parent Category')
             ->setAttribs(array('class'=>'span4'))
             ->setRequired(false);

        $element->addMultiOption('', '');
        $categories = new Categories_Model_Category_Table();
        $select = $categories->select()->order('path');
        foreach ($categories->fetchAll($select) as $row) {
            $element->addMultiOption($row->id, str_repeat("…", $row->level) . " " . $row->title);
        }
        return $element;
    }
}