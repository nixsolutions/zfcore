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
class Blog_Form_Admin_Create extends Zend_Form
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
            ->setAttribs(array('style'=>'width:60%'))
            ->addValidator(
                'regex',
                false,
                array('/^[\w\s\'",.\-_]+$/i', 'messages' => array (
                    Zend_Validate_Regex::INVALID => 'Invalid title',
                    Zend_Validate_Regex::NOT_MATCH  => 'Invalid title'
                ))
            );
        $this->addElement($title);

        $teaser = new Core_Form_Element_Wysiwyg('teaser');
        $teaser->setLabel('Teaser')
               ->setAttribs(array('style' => 'width:90%;height:200px'))
               ->addToolbar(
           array(
               'biu',
               array('indent', 'outdent'),
               'justify',
               'linkToggle',
               'removeFormat'
           )
        );
        $this->addElement($teaser);
        $teaser = new Core_Form_Element_Wysiwyg('body');
        $teaser->addToolbar(
            array(
                'biu',
                array('indent', 'outdent'),
                'justify',
                'linkToggle',
                'image',
                'removeFormat'
            )
        )->addToolbar(
           array(
               array('p', 'quote', 'br'),
               'formatBlock',
               'fontFace',
               'fontSize',
               'hiliteColor',
               'foreColor'
           )
        )->setUploadPath('/upload');

        $teaser->setLabel('Text')
               ->setAttribs(array('style' => 'width:90%;height:340px'));
        $this->addElement($teaser);

        $published = new Zend_Form_Element_Text('published');
        $published->setLabel('Published Date')
            ->setRequired(true);
        $this->addElement($published);

        $this->addElement($this->_category());
        $this->addElement($this->_status());
        $this->addElement($this->_user());


        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save');
        $this->addElement($submit);

        $this->addElement(new Zend_Form_Element_Hidden('pid'));
    }

    /**
     * Category Combobox
     *
     * @return Zend_Form_Element_Select
     */
    protected function _category()
    {
        $categories = new Forum_Model_Category_Manager();

        $options = array();
        foreach ($categories->getAll() as $category) {
            $options[$category->id] = $category->title;
        }

        $element = new Zend_Form_Element_Select('categoryId');
        $element->setLabel('Category')
                ->setRequired(true)
                ->addMultioptions($options)
                ->setAttribs(array('style' => 'width:60%'));


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
                ->setAttribs(array('style' => 'width:60%'))
                ->addMultioptions(
                    array(
                         Forum_Model_Post::STATUS_ACTIVE => Forum_Model_Post::STATUS_ACTIVE,
                         Forum_Model_Post::STATUS_CLOSED => Forum_Model_Post::STATUS_CLOSED,
                         Forum_Model_Post::STATUS_DELETED => Forum_Model_Post::STATUS_DELETED,
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
                ->setAttribs(array('style' => 'width:60%'))
                ->addMultioptions($res);
        return $element;
    }
}