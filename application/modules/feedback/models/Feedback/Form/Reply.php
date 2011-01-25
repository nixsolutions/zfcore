<?php
/**
 * Replying to a message, the form
 * 
 * @category Application
 * @package Model
 * @subpackage Form
 * 
 * @version  $Id: Reply.php 1561 2009-10-16 13:31:31Z dark $
 */
class Feedback_Model_Feedback_Form_Reply extends Zend_Dojo_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('feedbackForm')
             ->setMethod(Zend_Dojo_Form::METHOD_POST)
             ->setAttrib('enctype', Zend_Dojo_Form::ENCTYPE_MULTIPART);
        
        $this->addElements(
            array(
                $this->_subject(),
                $this->_body(),
                $this->_sender(),
                $this->_email(),
                $this->_fromName(),
                $this->_fromEmail(),
                //$this->_template(),
                $this->_file(),
                //$this->_copy(),
                $this->_button('Send', 'submit'),
                $this->_button('Reset', 'reset'),
                $this->_hidden('id')
            )
        );
        
        return $this;
    }
    
    /**
     * Create feedback sender element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _sender()
    {
        $element = new Zend_Dojo_Form_Element_ValidationTextBox('sender');
        $element->setLabel('Sender')
                ->setRequired(false)
                ->setAttribs(array('style' => 'width:60%'))
                ->setTrim(true)
                ->addValidator('StringLength', false, array('max' => '255'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
              
        return $element;
    }
    
    /**
     * Create feedback email element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _email()
    {
        $element = new Zend_Dojo_Form_Element_ValidationTextBox('email');
        $element->setLabel('Email')
                ->setRequired(false)
                ->setAttribs(array('style' => 'width:60%'))
                ->setTrim(true)
                ->setRegExp('[\w\.\-\_\d]+\@[\w\.\-\_\d]+\.\w+')
                ->addValidator('StringLength', false, array('max' => '80'))
                ->addValidator('EmailAddress')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
              
        return $element;
    }
    
    /**
     * Create feedback subject element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _subject()
    {
        $element = new Zend_Dojo_Form_Element_ValidationTextBox('subject');
        $element->setLabel('Subject')
                ->setRequired(false)
                ->setAttribs(array('style' => 'width:60%'))
                ->setTrim(true)
                ->addValidator('StringLength', false, array('max' => '255'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
              
        return $element;
    }
    
    /**
     * Create feedback template element
     *
     * @return object Zend_Dojo_Form_Element_ComboBox
     */
    protected function _template()
    {
        $mailTemplates = new Model_Mail_Table();
        $templates = $mailTemplates->fetchAll();
        $options = array();
        foreach ($templates as $template) {
            $options[$template->id] = $template->description;
        }
        $element = new Zend_Dojo_Form_Element_ComboBox('template');
        $element->setLabel('By Template')
                ->setAttribs(array('style' => 'width:60%'))
                ->setRequired(false)
                ->setMultiOptions($options);
        return $element;
    }
    
    /**
     * Create feedback body element
     *
     * @return object Zend_Dojo_Form_Element_Editor
     */
    protected function _body()
    {
        $element = new Zend_Dojo_Form_Element_Editor(
            'message',
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
                                         'Preview', '|',
                                         'ViewSource')        
            )
        );



        $element->setLabel('Body')
                ->setRequired(true)
                ->setAttribs(array('style' => 'width:60%;height:300px'))
                ->addFilter('StringTrim');
                
        return $element;
    }
    
    /**
     * Create feedback file element
     *
     * @return object Zend_Form_Element_File
     */
    protected function _file()
    {
        $element = new Zend_Form_Element_File('inputFile', array('ignore' => true));
        $element->setLabel('Attachment Image: ')
                ->setRequired(false)
                ->setAttribs(array('dojoType' => 'dojox.form.FileInput'))
                // Deprecated:
                //->setDestination(APPLICATION_PATH . '/../data/uploads')
                // New method:
                ->addFilter('Rename', APPLICATION_PATH . '/../data/uploads')
                ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
                //->addValidator('Count', false, 1)
                ->addValidator('Size', false, 2097152) 
                ->setMaxFileSize(2097152) // limits the filesize on the client side
                ->addFilter('BaseName');
           
        return $element;
    }
    
    /**
     * Create feedback copy element
     *
     * @return object Zend_Dojo_Form_Element_CheckBox
     */
    protected function _copy()
    {
        $element = new Zend_Dojo_Form_Element_CheckBox('saveCopy');
        $element->setLabel('Save copy message')
                ->setRequired(false)
                ->setValue('0')
                ->addFilter('StripTags')
                ->setDecorators(
                    array(
                        new Zend_Dojo_Form_Decorator_DijitElement(),
                        new Zend_Form_Decorator_Label(array('placement' => Zend_Form_Decorator_Abstract::APPEND)),
                        new Zend_Form_Decorator_HtmlTag(array('tag' => 'dd')),
                    )
                );
                
        return $element;
    }

    
    /**
     * Create fromName mail element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _fromName()
    {
        $fromName = new Zend_Dojo_Form_Element_ValidationTextBox('fromName');
        $fromName->setLabel('From Name')
                 ->setRequired(false)
                 ->setAttribs(array('style'=>'width:60%'))
                 ->setTrim(true)
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim');

        return $fromName;
    }

    
    /**
     * Create fromEmail email element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _fromEmail()
    {
        $fromEmail = new Zend_Dojo_Form_Element_ValidationTextBox('fromEmail');
        $fromEmail->setLabel('From Email')
                  ->setRequired(false)
                  ->setAttribs(array('style'=>'width:60%'))
                  ->setTrim(true)
                  ->setRegExp('[\w\.\-\_\d]+\@[\w\.\-\_\d]+\.\w+')
                  ->setValue(null)
                  ->addValidator('StringLength', false, array(6))
                  ->addValidator('EmailAddress');

        return $fromEmail;
    }

    
    /**
     * Create feedback button element
     *
     * @param string $label
     * @param string $type
     * @param string $name
     * @return object Zend_Dojo_Form_Element_Button
     */
    protected function _button($label = 'Button', $type = 'button', $name = '')
    {
        if (!strlen($name)) {
            $name = str_replace(' ', '-', strtolower($label));
        }
        $element = new Zend_Dojo_Form_Element_Button($name);
        $element->setLabel($label)
                ->setRequired(false)
                ->setAttribs(array('type' => $type))
                ->setDecorators(array(new Zend_Dojo_Form_Decorator_DijitElement()));
             
        return $element;
    }
    
    /**
     * Create feedback hidden element
     *
     * @param string $name
     * @return object Zend_Form_Element_Hidden
     */
    protected function _hidden($name, $value = '')
    {
        $element = new Zend_Form_Element_Hidden($name);
        $element->setRequired(false)
                ->setValue($value)
                ->addFilter('StripTags')
                ->setDecorators(array(new Zend_Dojo_Form_Decorator_DijitElement()));
             
        return $element;
    }
}
