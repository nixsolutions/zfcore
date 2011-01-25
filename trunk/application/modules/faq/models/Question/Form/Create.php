<?php
/**
 * Create form
 * 
 * @category   Application
 * @package    Faq
 * @subpackage Form
 */
class Faq_Model_Question_Form_Create extends Zend_Dojo_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('questionForm');
        $this->setMethod('post');
        
        $this->addElement(
            'Editor', 'question',
            array(
                'label'    => 'Question',
                'required' => true,
                'attribs'  => array('style' => 'width:100%;height:340px'),
                'plugins'  => array(
                    'undo', 'redo', 'cut', 'copy', 'paste', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'subscript', 'superscript', 'removeFormat', '|',
                    //'fontName', 'fontSize', 'formatBlock', 'foreColor', 'hiliteColor','|',
                    'indent', 'outdent', 'justifyCenter', 'justifyFull',
                    'justifyLeft', 'justifyRight', 'delete', '|',
                    'insertOrderedList', 'insertUnorderedList', 'insertHorizontalRule', '|',
                    //'LinkDialog', 'UploadImage', '|',
                    //'ImageManager',
                    'FullScreen', '|',
                    'ViewSource'
                )
            )
        );
        
        $this->addElement(
            'Editor', 'answer', array(
                'label'    => 'Answer',
                'required' => true,
                'attribs'  => array('style' => 'width:100%;height:340px'),
                'plugins'  => array(
                    'undo', 'redo', 'cut', 'copy', 'paste', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'subscript', 'superscript', 'removeFormat', '|',
                    //'fontName', 'fontSize', 'formatBlock', 'foreColor', 'hiliteColor','|',
                    'indent', 'outdent', 'justifyCenter', 'justifyFull',
                    'justifyLeft', 'justifyRight', 'delete', '|',
                    'insertOrderedList', 'insertUnorderedList', 'insertHorizontalRule', '|',
                    //'LinkDialog', 'UploadImage', '|',
                    //'ImageManager',
                    'FullScreen', '|',
                    'ViewSource'
                )
            )
        );

        $this->addElement('SubmitButton', 'submit', array('label' => 'Save'));
        
        Zend_Dojo::enableForm($this);
    }
}