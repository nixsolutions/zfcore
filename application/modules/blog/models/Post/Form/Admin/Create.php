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


        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')
            ->setRequired(true)
            ->setAttribs(array('style'=>'width:60%'))
            ->addValidator('regex',
                false,
                array('/^[\w\s\'",.\-_]+$/i', 'messages' => array (
                    Zend_Validate_Regex::INVALID => 'Invalid title',
                    Zend_Validate_Regex::NOT_MATCH  => 'Invalid title'
                )));
        $this->addElement($title);

//        $this->addElement(
//            'ValidationTextBox', 'title',
//            array(
//                'label'      => 'Title',
//                'required'   => true,
//                'attribs'    => array('style'=>'width:60%'),
//                'regExp'     => '^[\w\s\'",.\-_]+$',
//                'validators' => array(
//                    array(
//                        'regex',
//                        false,
//                        array('/^[\w\s\'",.\-_]+$/i', 'messages' => array (
//                            Zend_Validate_Regex::INVALID => 'Invalid title',
//                            Zend_Validate_Regex::NOT_MATCH  => 'Invalid title'
//                        ))
//                    ),
//                )
//            )
//        );

        $this->addElement(
            'Editor', 'teaser',
            array(
                'label'      => 'Teaser',
                'required'   => true,
                'attribs'    => array('style' => 'width:100%;height:100px'),
                'plugins'    => array('undo', 'redo', 'cut', 'copy', 'paste', '|',
                                      'bold', 'italic', 'underline', 'strikethrough', '|',
                                      'subscript', 'superscript', 'removeFormat', '|',
                                      //'fontName', 'fontSize', 'formatBlock', 'foreColor', 'hiliteColor','|',
                                      'indent', 'outdent', 'justifyCenter', 'justifyFull',
                                      'justifyLeft', 'justifyRight', 'delete', '|',
                                      'insertOrderedList', 'insertUnorderedList', 'insertHorizontalRule', '|',
                                      //'LinkDialog', 'UploadImage', '|',
                                      //'ImageManager',
                                      'FullScreen', '|',
                                      'ViewSource')
            )
        );

        $this->addElement(
            'Editor', 'body',
            array(
                'label'      => 'Text',
                'required'   => true,
                'attribs'    => array('style' => 'width:100%;height:340px'),
                'plugins'    => array('undo', 'redo', 'cut', 'copy', 'paste', '|',
                                      'bold', 'italic', 'underline', 'strikethrough', '|',
                                      'subscript', 'superscript', 'removeFormat', '|',
                                      //'fontName', 'fontSize', 'formatBlock', 'foreColor', 'hiliteColor','|',
                                      'indent', 'outdent', 'justifyCenter', 'justifyFull',
                                      'justifyLeft', 'justifyRight', 'delete', '|',
                                      'insertOrderedList', 'insertUnorderedList', 'insertHorizontalRule', '|',
                                      //'LinkDialog', 'UploadImage', '|',
                                      //'ImageManager',
                                      'FullScreen', '|',
                                      'ViewSource')
            )
        );
        $this->addElement(
            'DateTextBox', 'published',
            array(
                'label'      => 'Published Date',
                'required'   => true
            )
        );

        $this->addElement($this->_category());

        $this->addElement($this->_status());

        $this->addElement($this->_user());

        $this->addElement(
            'SubmitButton',
            'submit',
            array(
                'label' => 'Save'
            )
        );

        $this->addElement(new Zend_Form_Element_Hidden('pid'));

        Zend_Dojo::enableForm($this);
    }

    /**
     * Category Combobox
     *
     * @return Zend_Dojo_Form_Element_ComboBox
     */
    protected function _category()
    {
        $categories = new Blog_Model_Category_Manager();

        $element = new Zend_Dojo_Form_Element_FilteringSelect('categoryId');
        $element->setLabel('Category')
                ->setRequired(true)
                ->setAttribs(array('style'=>'width:60%'));

        foreach ($categories->getAll() as $category) {
             $element->addMultiOption($category->id, $category->title);
        }

        return $element;
    }

    /**
     * Status Combobox
     *
     * @return Zend_Dojo_Form_Element_ComboBox
     */
    protected function _status()
    {
        $element = new Zend_Dojo_Form_Element_FilteringSelect('status');
        $element->setLabel('Status')->setRequired(true)
                ->setAttribs(array('style'=>'width:60%'));

        $element->addMultiOption(Blog_Model_Post::STATUS_DRAFT, 'Draft');
        $element->addMultiOption(Blog_Model_Post::STATUS_PUBLISHED, 'Published');
        $element->addMultiOption(Blog_Model_Post::STATUS_DELETED,'Deleted');

        return $element;
    }

    /**
     * User Combobox
     *
     * @return Zend_Dojo_Form_Element_ComboBox
     */
    protected function _user()
    {
        $element = new Zend_Dojo_Form_Element_FilteringSelect('userId');
        $element->setLabel('Author')->setRequired(true)
        ->setAttribs(array('style'=>'width:60%'));

        $users = new Users_Model_Users_Table();
        foreach ($users->fetchAll() as $row) {
            $element->addMultiOption($row->id, $row->login);
        }

        return $element;
    }
}