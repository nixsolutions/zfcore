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
class Forum_Form_Post_Create extends Zend_Form
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

        $this->addElement($this->_category());

        $this->addElement(
            'select', 'status',
            array(
                'label' => 'status',
                'required' => true,
                'multiOptions' => array(
                    Forum_Model_Post::STATUS_ACTIVE  => Forum_Model_Post::STATUS_ACTIVE,
                    Forum_Model_Post::STATUS_CLOSED  => Forum_Model_Post::STATUS_CLOSED,
                    Forum_Model_Post::STATUS_DELETED => Forum_Model_Post::STATUS_DELETED,
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
        $category = new Zend_Form_Element_Select('categoryId');
        $category->setLabel('categoryId');
        $category->setRequired(true);

        $cats = new Forum_Model_Category_Manager();
        foreach ($cats->getAll() as $cat) {
            $category->addMultiOption($cat->id, $cat->title);
        }

        return $category;
    }
}