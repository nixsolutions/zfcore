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

        $this->addElement(
            'text', 'title',
            array(
                'label' => 'title',
                'required' => true,
                'filters' => array('StringTrim'),
            )
        );

        $text = new Core_Form_Element_TinyMCE(
            'text', array(
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

        $this->addElement($this->_category());

        $this->addElement(
            'select', 'status',
            array(
                'label' => 'status',
                'required' => true,
                'multiOptions' => array(
                    Blog_Model_Post::STATUS_ACTIVE  => Blog_Model_Post::STATUS_ACTIVE,
                    Blog_Model_Post::STATUS_CLOSED  => Blog_Model_Post::STATUS_CLOSED,
                    Blog_Model_Post::STATUS_DELETED => Blog_Model_Post::STATUS_DELETED,
                ),
            )
        );

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
        $cats = new Blog_Model_Category_Manager();
        $categories = $cats->getAll();

        $category = new Zend_Form_Element_Select('category');
        $category->setLabel('category');
        $category->setRequired(true);
        foreach ($categories as $cat) {
            $category->addMultiOption($cat->id, $cat->title);
        }

        return $category;
    }
}