<?php
/**
 * Database form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id$
 */
class Install_Form_Settings_Mail extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('settingsForm');
        $this->setMethod('post');

        $this->addElement($this->_transport());

        $this->addElement($this->_host());

        $this->addElement($this->_port());

        $this->addElement($this->_email());

        $this->addElement($this->_name());

        $this->addElement($this->_submit());

        return $this;
    }

    protected function _submit()
    {
        $sudmit = new Zend_Form_Element_Submit('submit');
        $sudmit->setLabel('Save & Next >');
        return $sudmit;
    }

    protected function _transport()
    {
        $element = new Zend_Form_Element_Select('type');
        $element->setLabel('Transport');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        $element->addMultiOption('Zend_Mail_Transport_Smtp', 'Smtp');
        $element->addMultiOption('Zend_Mail_Transport_Sendmail', 'Sendmail');
        //$element->addMultiOption('File', 'Zend_Mail_Transport_File');

        return $element;
    }

    protected function _host()
    {
        $element = new Zend_Form_Element_Text('host');
        $element->setLabel('Hostname');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        $element->setValue('localhost');

        $element->addValidator(new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_LOCAL));

        return $element;
    }

    protected function _port()
    {
        $element = new Zend_Form_Element_Text('port');
        $element->setLabel('Port');
        $element->setRequired(true)->setAttrib('style', 'width:100%');
        $element->setValue(25);

        $element->addValidator(new Zend_Validate_Int());

        return $element;
    }

    protected function _email()
    {
        $element = new Zend_Form_Element_Text('email');
        $element->setLabel('Email');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        $element->addValidator(new Zend_Validate_EmailAddress());

        return $element;
    }

    protected function _name()
    {
        $element = new Zend_Form_Element_Text('name');
        $element->setLabel('Name');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        $element->addValidator(new Zend_Validate_Alnum(true));

        return $element;
    }
}