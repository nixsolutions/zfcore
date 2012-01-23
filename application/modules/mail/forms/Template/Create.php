<?php
/**
 * Mail_Form_Template_Create
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Create.php 206 2010-10-20 10:55:55Z AntonShevchuk $
 */
class Mail_Form_Template_Create extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('mailCreateForm')
             ->setMethod('post');

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
              ->setAttribs(array('style'=>'width:60%'));
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
                ->setRequired(true)
                ->setAttribs(array('style'=>'width:60%'))
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
        $body = new Core_Form_Element_Redactor('bodyHtml');
        $body->setLabel('Body')
             ->setRequired(true)
             ->setAttribs(array('style'=>'width:60%'))
             ->addFilter('StringTrim')
             ->setAttrib(
                 'redactor',
                 array(
                    'toolbar' => 'full',
                    'image_upload' => $this->_getUploadImageUrl()
                 )
             );

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
             ->setAttrib('cols', 20)
             ->setAttrib('rows', 20)
             ->setAttribs(array('style'=>'width:60%'))
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
             ->setRequired(false)
             ->setAttribs(array('style'=>'width:60%'))
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
                 ->setRequired(false)
                 ->setAttribs(array('style'=>'width:60%'))
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
                  ->setRequired(false)
                  ->setAttribs(array('style'=>'width:60%'))
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
               ->setRequired(false)
               ->setAttribs(array('style'=>'width:60%'))
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
                ->setRequired(false)
                ->setAttribs(array('style'=>'width:60%'))
                ->setValue(null)
                ->addValidator('StringLength', false, array(6))
                ->addValidator('EmailAddress');

        return $toEmail;
    }

    /**
     * Create submit element
     *
     * @return object Zend_Form_Element_Text
     */
    protected function _submit()
    {
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Create');

        return $submit;
    }

    /**
     * get upload image url
     *
     * @return string
     */
    protected function _getUploadImageUrl()
    {
        $helper = new Zend_View_Helper_Url();
        return $helper->url(
            array(
                'module' => 'pages',
                'controller' => 'management',
                'action' => 'upload'
            ),
            'default',
            true
        );
    }
}