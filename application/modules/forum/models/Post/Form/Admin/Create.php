<?php
/**
 * Login form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Login.php 1561 2009-10-16 13:31:31Z dark $
 */
class Forum_Model_Post_Form_Admin_Create extends Zend_Form
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
            'Text', 'title',
            array(
                'label'      => 'Title',
                'required'   => true,
                'attribs'    => array('style'=>'width:60%'),
                'validators' => array(
                    array(
                        'regex',
                        false,
                        array('/^[\w\s\'",.\-_]+$/i', 'messages' => array (
                            Zend_Validate_Regex::INVALID => 'Invalid title',
                            Zend_Validate_Regex::NOT_MATCH  => 'Invalid title'
                        ))
                    ),
                )
            )
        );

        $this->addElement(
            new Core_Form_Element_Redactor(
                'body',
                array(
                    'label'      => 'Post',
                    'required'   => true,
                    'attribs'    => array('style' => 'width:100%;height:340px'),
                )
            )
        );

        $this->addElement($this->_category());

        $this->addElement($this->_status());

        $this->addElement(
            'Submit',
            'submit',
            array(
                'label' => 'Save'
            )
        );

        $this->addElement(new Zend_Form_Element_Hidden('pid'));
    }

    /**
     * Category
     *
     * @return Zend_Form_Element_Select
     */
    protected function _category()
    {
        $categories = new Forum_Model_Category_Manager();

        $element = new Zend_Form_Element_Select('categoryId');
        $element->setLabel('Category')
                ->setRequired(true)
                ->setAttribs(array('style'=>'width:60%'));

        foreach ($categories->getAll() as $category) {
             $element->addMultiOption($category->id, $category->title);
        }

        return $element;
    }

    /**
     * Status
     *
     * @return Zend_Form_Element_Select
     */
    protected function _status()
    {
        $status = new Zend_Form_Element_Select('status');
        $status->setLabel('Status')
               ->setRequired(true)
               ->setAttribs(array('style'=>'width:60%'))
               ->setMultiOptions(
                   array(
                       Forum_Model_Post::STATUS_ACTIVE  => Forum_Model_Post::STATUS_ACTIVE,
                       Forum_Model_Post::STATUS_CLOSED  => Forum_Model_Post::STATUS_CLOSED,
                       Forum_Model_Post::STATUS_DELETED => Forum_Model_Post::STATUS_DELETED,
                   )
               );

        return $status;
    }
}