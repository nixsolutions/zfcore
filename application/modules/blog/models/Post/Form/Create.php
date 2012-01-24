<?php
/**
 * Login form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Create.php 162 2010-07-12 14:58:58Z AntonShevchuk $
 */
class Blog_Model_Post_Form_Create extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('postForm');
        $this->setMethod('post');

        $this->addElement($this->_category());

        $this->addElement(
            'text', 'title',
            array(
                'label' => 'title',
                'required' => true,
                'filters' => array('StringTrim'),
            )
        );

        $teaser = new Core_Form_Element_TinyMCE(
            'teaser', array(
                'label' => 'teaser',
                'cols'  => '75',
                'rows'  => '5',
                'required' => true,
                'filters'  => array('StringTrim'),
                'tinyMCE' => array(
                    'mode' => "textareas",
                    'theme' => "simple",
                ),
            )
        );
        $this->addElement($teaser);

        $text = new Core_Form_Element_TinyMCE(
            'body', array(
                'label' => 'text',
                'cols'  => '75',
                'rows'  => '20',
                'required' => true,
                'filters'  => array('StringTrim'),
                'tinyMCE' => array(
                    'mode' => "textareas",
                    'theme' => "simple",
                ),
            )
        );
        $this->addElement($text);

        $this->addElement($this->_status());

        $this->addElement($this->_submit());

        return $this;
    }

    protected function _submit()
    {
        $sudmit = new Zend_Form_Element_Submit('submit');
        $sudmit->setLabel('create');
        return $sudmit;
    }

    protected function _category()
    {
        $category = new Zend_Form_Element_Select('categoryId');
        $category->setLabel('categoryId');
        $category->setRequired(true)->setAttrib('style', 'width:100%');

        $cats = new Blog_Model_Category_Manager();
        foreach ($cats->getAll() as $cat) {
            $category->addMultiOption($cat->id, $cat->title);
        }

        return $category;
    }

    /**
     * Status Combobox
     *
     * @return Zend_Form_Element_Select
     */
    protected function _status()
    {
        $element = new Zend_Form_Element_Select('status');
        $element->setLabel('Status')->setRequired(true);

        $element->addMultiOption(Blog_Model_Post::STATUS_DRAFT, 'Draft');
        $element->addMultiOption(Blog_Model_Post::STATUS_PUBLISHED, 'Published');
        $element->addMultiOption(Blog_Model_Post::STATUS_DELETED, 'Deleted');

        return $element;
    }
}