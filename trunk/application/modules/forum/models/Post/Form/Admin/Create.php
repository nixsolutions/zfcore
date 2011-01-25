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
class Forum_Model_Post_Form_Admin_Create extends Zend_Dojo_Form
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
            'ValidationTextBox', 'post_title',
            array(
                'label'      => 'Title',
                'required'   => true,
                'attribs'    => array('style'=>'width:60%'),
                'regExp'     => '^[\w\s\'",.\-_]+$',
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
            'Editor', 'post_text',
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
        
        $this->addElement($this->_category()); 
        
        $this->addElement($this->_status());

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
        $cats = new Forum_Model_Category_Manager();
        $categories = $cats->getAllCategories();

        $category = new Zend_Dojo_Form_Element_ComboBox('ctg_id');
        $category->setLabel('Category')
                 ->setRequired(true)
                 ->setAttribs(array('style'=>'width:60%'))
                 ->setMultiOptions($categories);

        return $category;
    }

    /**
     * Status combobox
     *
     * @return Zend_Dojo_Form_Element_ComboBox
     */
    protected function _status()
    {
        $status = new Zend_Dojo_Form_Element_ComboBox('post_status');
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