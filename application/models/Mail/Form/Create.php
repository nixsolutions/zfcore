<?php
/**
 * Register user form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Create.php 206 2010-10-20 10:55:55Z AntonShevchuk $
 */
class Model_Mail_Form_Create extends Zend_Dojo_Form
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
                 $this->_signature(),
                 $this->_submit()
            )
        );
        return $this;
    }

    /**
     * Create mail alias element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _alias()
    {
        $alias = new Zend_Dojo_Form_Element_ValidationTextBox('alias');
        $alias->setLabel('Alias')
              ->setIgnore(true)
              ->setDijitParam('disabled', 'on')
              ->setAttribs(array('style'=>'width:60%'));

        return $alias;
    }

    /**
     * Create mail subject element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _subject()
    {
        $subject = new Zend_Dojo_Form_Element_ValidationTextBox('subject');
        $subject->setLabel('Subject')
                ->setRequired(true)
                ->setTrim(true)
                ->setAttribs(array('style'=>'width:60%'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        return $subject;
    }

    /**
     * Create mail body element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _body()
    {
        $body = new Zend_Dojo_Form_Element_Editor('body');
        $body->setLabel('Body')
             ->setRequired(true)
             ->setAttribs(array('style'=>'width:60%'))
             ->addFilter('StringTrim');
        return $body;
    }

    /**
     * Create mail body element (text)
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _altBody()
    {
        $body = new Zend_Dojo_Form_Element_Textarea('altBody');
        $body->setLabel('Body (text)')
             ->setRequired(true)
             ->setAttribs(array('style'=>'width:60%'))
             ->addFilter('StringTrim');
        return $body;
    }

    /**
     * Create mail description element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _description()
    {
        $desc = new Zend_Dojo_Form_Element_ValidationTextBox('description');
        $desc->setLabel('Description')
             ->setRequired(false)
             ->setTrim(true)
             ->setAttribs(array('style'=>'width:60%'))
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        return $desc;
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
     * Create toName mail element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _toName()
    {
        $toName = new Zend_Dojo_Form_Element_ValidationTextBox('toName');
        $toName->setLabel('To Name')
               ->setRequired(false)
               ->setAttribs(array('style'=>'width:60%'))
               ->setTrim(true)
               ->addFilter('StripTags')
               ->addFilter('StringTrim');

        return $toName;
    }

    /**
     * Create toEmail email element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _toEmail()
    {
        $toEmail = new Zend_Dojo_Form_Element_ValidationTextBox('toEmail');
        $toEmail->setLabel('To Email')
                ->setRequired(false)
                ->setAttribs(array('style'=>'width:60%'))
                ->setTrim(true)
                ->setRegExp('[\w\.\-\_\d]+\@[\w\.\-\_\d]+\.\w+')
                ->setValue(null)
                ->addValidator('StringLength', false, array(6))
                ->addValidator('EmailAddress');

        return $toEmail;
    }

    /**
     * Create submit element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _submit()
    {
        $submit = new Zend_Dojo_Form_Element_SubmitButton('submit');
        $submit->setLabel('Create');

        return $submit;
    }

    /**
     * Create element signature
     */
    protected function _signature()
    {
        $signature = new Zend_Dojo_Form_Element_CheckBox('signature');
        $signature->setLabel('Enable Layout with signature');
        return $signature;
    }
}