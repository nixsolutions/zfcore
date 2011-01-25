<?php
/**
 * Edit page form
 * 
 * @category Application
 * @package Model
 * @subpackage Form
 * 
 * @version  $Id: Edit.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Pages_Model_Page_Form_Edit extends Zend_Dojo_Form
{
    /**
     * Form initialization
     *
     * @todo Add unique validator for aliases
     * 
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('enctype', 'multipart/form-data');
        
        $this
             ->addElement(
                 'ValidationTextBox', 'title',
                 array(
                     'label'      => 'Title',
                     'required'   => true,
                     'attribs'    => array('style'=>'width:60%'),
                     'regExp'     => '^[\w\s\'",.\-_]+$',
                     'validators' => array(
                         array(
                             'regex', false, array('/^[\w\s\'",.\-_]+$/i', 'messages' => array (
                                 Zend_Validate_Regex::INVALID => 'Invalid title',
                                 Zend_Validate_Regex::NOT_MATCH  => 'Invalid title'))
                         ),
                     )
                 )
             )
             ->addElement(
                 'ValidationTextBox', 'alias',
                 array(
                     'label'      => 'Alias (for permalink)',
                     'required'   => true,
                     'attribs'    => array('style'=>'width:60%'),
                     'regExp'     => '^[a-z0-9\-\_]+$',
                     'validators' => array(
                            array(
                                'NotEmpty', true, array('messages' => array (
                                    Zend_Validate_NotEmpty::IS_EMPTY  => 'Page alias is required'
                                ))
                            ),                        
                            array(
                                'regex', false, array('/^[a-z0-9\-\_]+$/i', 'messages' => array (
                                    Zend_Validate_Regex::INVALID  => 'Invalid page alias',
                                    Zend_Validate_Regex::NOT_MATCH  => 'Invalid page alias'
                                ))
                            ),
                     )
                 )
             )
             ->addElement(
                 'Editor', 'content',
                 array(
                     'styleSheets'   => array('/layouts/default/css/style.css'),
                     'attribs'       => array('style' => 'width:100%;height:340px'),
                     'plugins'       => array('undo', 'redo', 'cut', 'copy', 'paste', '|',
                                             'bold', 'italic', 'underline', 'strikethrough', '|',
                                             'subscript', 'superscript', 'removeFormat', '|',
                                             //'fontName', 'fontSize', 'formatBlock', 'foreColor', 'hiliteColor', '|',
                                             'indent', 'outdent', 'justifyCenter', 'justifyFull',
                                             'justifyLeft', 'justifyRight', 'delete', '|',
                                             'insertOrderedList', 'insertUnorderedList', 'insertHorizontalRule', '|',
                                             //'LinkDialog', 'UploadImage', '|',
                                             'ImageManager',
                                             'FullScreen', '|',
                                             //'Preview', '|',
                                             'ViewSource')
                 )
             )
             ->addElement(
                 'ValidationTextBox', 'keywords',
                 array(
                     'label'      => 'Meta Keywords',
                     'attribs'    => array('style' => 'width:60%'),
                     'regExp'     => '^[\w\s\,\.\-\_]+$',
                     'validators' => array(
                         array(
                             'regex', false, array('/^[\w\s\,\.\-\_]+$/i', 'messages' => array (
                                 Zend_Validate_Regex::INVALID  => 'Invalid meta keywords',
                                 Zend_Validate_Regex::NOT_MATCH  => 'Invalid meta keywords'
                             ))
                         ),
                     )
                 )
             )
             ->addElement(
                 'ValidationTextBox', 'description',
                 array(
                     'label'      => 'Meta Description',
                     'attribs'    => array('style' => 'width:60%'),
                     'regExp'     => '^[\w\s\,\.\-\_]+$',
                     'validators' => array(
                         array(
                             'regex', false, array('/^[\w\s\,\.\-\_]+$/i', 'messages' => array (
                                 Zend_Validate_Regex::INVALID  => 'Invalid meta description',
                                 Zend_Validate_Regex::NOT_MATCH  => 'Invalid meta description'
                             ))
                         ),
                     )
                 )
             )
             ->addElement(
                 'SubmitButton', 'submit',
                 array(
                    'label' => 'Save'
                 )
             );
        
        $this->addElement(new Zend_Form_Element_Hidden('pid'));
        
        Zend_Dojo::enableForm($this);
    }
}