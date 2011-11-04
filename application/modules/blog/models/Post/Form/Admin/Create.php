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
class Blog_Model_Post_Form_Admin_Create extends Zend_Form
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
            new Core_Form_Element_Redactor('teaser',
                array(
                    'label'      => 'Teaser',
                    'required'   => true,
                    'attribs'    => array('style' => 'width:100%;height:100px'),
                )
            )
        );

        $this->addElement(
            new Core_Form_Element_Redactor('body',
                array(
                    'label'      => 'Text',
                    'required'   => true,
                    'attribs'    => array('style' => 'width:100%;height:340px')
                )
            )
        );

        $this->addElement(
            'Text', 'published',
            array(
                'label'      => 'Published Date',
                'required'   => true
            )
        );

        $this->addElement($this->_category());

        $this->addElement($this->_status());

        $this->addElement($this->_user());

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
     * Category Combobox
     *
     * @return Zend_Form_Element_Select
     */
    protected function _category()
    {
        $categories = new Blog_Model_Category_Manager();

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
        $element = new Zend_Form_Element_Select('status');
        $element->setLabel('Status')->setRequired(true)
                ->setAttribs(array('style'=>'width:60%'));

        $element->addMultiOption(Blog_Model_Post::STATUS_DRAFT, 'Draft');
        $element->addMultiOption(Blog_Model_Post::STATUS_PUBLISHED, 'Published');
        $element->addMultiOption(Blog_Model_Post::STATUS_DELETED,'Deleted');

        return $element;
    }

    /**
     * User
     *
     * @return Zend_Form_Element_Select
     */
    protected function _user()
    {
        $element = new Zend_Form_Element_Select('userId');
        $element->setLabel('Author')->setRequired(true)
        ->setAttribs(array('style'=>'width:60%'));

        $users = new Users_Model_Users_Table();
        foreach ($users->fetchAll() as $row) {
            $element->addMultiOption($row->id, $row->login);
        }

        return $element;
    }
}