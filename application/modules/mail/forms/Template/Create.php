<?php
/**
 *
 */
/**
 * Mail_Form_Template_Create
 *
 * @category   Application
 * @package    Model
 * @subpackage Form
 */
class Mail_Form_Template_Create extends Core_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('mailCreateForm')->setMethod('post');

        $this->addElements(
            array($this->_alias(),
                 $this->_description(),
                 $this->_subject(),
                 $this->_body(),
                 $this->_altBody(),
                 $this->_fromName(),
                 $this->_fromEmail(),
                 $this->_submit()
            )
        );
        return $this;
    }

    /**
     * Create mail alias element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _alias()
    {
        $alias = new Zend_Form_Element_Text('alias');
        $alias->setLabel('Alias')
              ->addDecorators($this->_inputDecorators)
              ->setAttribs(array('class'=>'span6'));
        return $alias;
    }

    /**
     * Create mail subject element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _subject()
    {
        $subject = new Zend_Form_Element_Text('subject');
        $subject->setLabel('Subject')
                ->addDecorators($this->_inputDecorators)
                ->setRequired(true)
                ->setAttribs(array('class'=>'span6'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        return $subject;
    }

    /**
     * Create mail body element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _body()
    {
        $body = new Core_Form_Element_Redactor(
            'bodyHtml', array(
                'label' => 'Content:',
                'cols'  => 50,
                'rows'  => 25,
                'required' => true,
                'filters' => array('StringTrim'),
                'redactor' => array(
                    'imageUpload'  => '/mail/images/upload/', // url or false
                    'imageGetJson' => '/mail/images/list/',
                    'fileUpload'   => '/admin/files/upload/',
                    'fileDownload' => '/admin/files/download/?file=',
                    'fileDelete'   => '/admin/files/delete/?file='))
        );
        $body->setRequired(true);
        return $body;
    }

    /**
     * Create mail body element (text)
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _altBody()
    {
        $body = new Zend_Form_Element_Textarea('bodyText');
        $body->setLabel('Body (text)')
             ->addDecorators($this->_inputDecorators)
             ->setAttrib('cols', 20)
             ->setAttrib('rows', 20)
             ->setAttribs(array('class'=>'span8'))
             ->addFilter('StringTrim');
        return $body;
    }

    /**
     * Create mail description element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _description()
    {
        $desc = new Zend_Form_Element_Text('description');
        $desc->setLabel('Description')
             ->addDecorators($this->_inputDecorators)
             ->setRequired(false)
             ->setAttribs(array('class'=>'span6'))
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        return $desc;
    }

    /**
     * Create fromName mail element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _fromName()
    {
        $fromName = new Zend_Form_Element_Text('fromName');
        $fromName->setLabel('From Name')
                 ->addDecorators($this->_inputDecorators)
                 ->setRequired(false)
                 ->setAttribs(array('class'=>'span4'))
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim');

        return $fromName;
    }

    /**
     * Create fromEmail email element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _fromEmail()
    {
        $fromEmail = new Zend_Form_Element_Text('fromEmail');
        $fromEmail->setLabel('From Email')
                  ->addDecorators($this->_inputDecorators)
                  ->setRequired(false)
                  ->setAttribs(array('class'=>'span4'))
                  ->setValue(null)
                  ->addValidator('StringLength', false, array(6))
                  ->addValidator('EmailAddress');

        return $fromEmail;
    }

    /**
     * Create toName mail element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _toName()
    {
        $toName = new Zend_Form_Element_Text('toName');
        $toName->setLabel('To Name')
               ->addDecorators($this->_inputDecorators)
               ->setRequired(false)
               ->setAttribs(array('class'=>'span4'))
               ->addFilter('StripTags')
               ->addFilter('StringTrim');

        return $toName;
    }

    /**
     * Create toEmail email element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _toEmail()
    {
        $toEmail = new Zend_Form_Element_Text('toEmail');
        $toEmail->setLabel('To Email')
                ->addDecorators($this->_inputDecorators)
                ->setRequired(false)
                ->setAttribs(array('class'=>'span4'))
                ->setValue(null)
                ->addValidator('StringLength', false, array(6))
                ->addValidator('EmailAddress');

        return $toEmail;
    }
}