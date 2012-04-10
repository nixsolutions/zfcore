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
class Blog_Form_Admin_Create extends Core_Form
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

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')
            ->setRequired(true)
            ->addDecorators($this->_inputDecorators)
            ->setAttribs(array('class'=>'span4'))
            ->addValidator(
                'regex',
                false,
                array('/^[\w\s\'",.\-_]+$/i', 'messages' => array (
                    Zend_Validate_Regex::INVALID => 'Invalid title',
                    Zend_Validate_Regex::NOT_MATCH  => 'Invalid title'
                ))
            );
        $this->addElement($title);

        $teaser = new Core_Form_Element_Redactor('teaser', array(
           'label' => 'Teaser',
           'cols'  => 50,
           'rows'  => 5,
           'required' => true,
           'filters' => array('StringTrim'),
           'redactor' => array(
               'imageUpload'  => false, // url or false
               'fileUpload'   => false,
           )
        ));
        $teaser->addDecorators($this->_inputDecorators);

        $this->addElement($teaser);

        $body = new Core_Form_Element_Redactor('body', array(
           'label' => 'Text',
           'cols'  => 50,
           'rows'  => 25,
           'required' => true,
           'filters' => array('StringTrim'),
           'redactor' => array(
               'imageUpload'  => '/blog/images/upload/', // url or false
               'fileUpload'   => '/blog/files/upload/',
               'fileDownload' => '/blog/files/download/?file=',
               'fileDelete'   => '/blog/files/delete/?file=',
           )
        ));
        $body->addDecorators($this->_inputDecorators);

        $this->addElement($body);

        $published = new Zend_Form_Element_Text('published');
        $published->setLabel('Published Date')
            ->setRequired(true)
            ->addDecorators($this->_inputDecorators);
        $this->addElement($published);

        $this->addElement($this->_category());
        $this->addElement($this->_status());
        $this->addElement($this->_user());

        $this->addElement($this->_submit());

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

        $options = array();
        foreach ($categories->getAll() as $category) {
            $options[$category->id] = $category->title;
        }

        $element = new Zend_Form_Element_Select('categoryId');
        $element->setLabel('Category')
                ->setRequired(true)
                ->addDecorators($this->_inputDecorators)
                ->addMultioptions($options)
                ->setAttribs(array('class' => 'span2'));


        return $element;
    }

    /**
     * Status combobox
     *
     * @return Zend_Form_Element_Select
     */
    protected function _status()
    {
        $status = new Zend_Form_Element_Select('status');
        $status->setLabel('Status')
                ->setRequired(true)
                ->addDecorators($this->_inputDecorators)
                ->setAttribs(array('class' => 'span2'))
                ->addMultioptions(
                    array(
                        Pages_Model_Page::STATUS_ACTIVE => Pages_Model_Page::STATUS_ACTIVE,
                        Pages_Model_Page::STATUS_CLOSED => Pages_Model_Page::STATUS_CLOSED,
                        Pages_Model_Page::STATUS_DELETED => Pages_Model_Page::STATUS_DELETED,
                    )
                );

        return $status;
    }

    /**
     * User Combobox
     *
     * @return Zend_Form_Element_Select
     */
    protected function _user()
    {
        $users = new Users_Model_Users_Table();
        $res = array();
        foreach ($users->fetchAll() as $row) {
            $res[$row->id] = $row->login;
        }

        $element = new Zend_Form_Element_Select('userId');
        $element->setLabel('Author')
                ->setRequired(true)
                ->setAttribs(array('class' => 'span2'))
                ->addMultioptions($res);
        return $element;
    }
}