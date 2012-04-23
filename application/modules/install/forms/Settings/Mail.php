<?php
/**
 * Mail form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Install_Form_Settings_Mail extends Core_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('mailForm');
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
        $sudmit = parent::_submit();
        $sudmit->setLabel('Next');
        return $sudmit;
    }

    protected function _transport()
    {
        $element = new Zend_Form_Element_Select('type');
        $element->setLabel('Transport')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4');

        $element->addMultiOption('Zend_Mail_Transport_Smtp', 'Smtp');
        $element->addMultiOption('Zend_Mail_Transport_Sendmail', 'Sendmail');
        //$element->addMultiOption('File', 'Zend_Mail_Transport_File');

        return $element;
    }

    protected function _host()
    {
        $element = new Zend_Form_Element_Text('host');
        $element->setLabel('Hostname')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4');

        $element->setValue('localhost');

        $element->addValidator(new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_LOCAL));

        return $element;
    }

    protected function _port()
    {
        $element = new Zend_Form_Element_Text('port');
        $element->setLabel('Port')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span2');
        $element->setValue(25);

        $element->addValidator(new Zend_Validate_Int());

        return $element;
    }

    protected function _email()
    {
        $element = new Zend_Form_Element_Text('email');
        $element->setLabel('Email')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4');

        $element->addValidator(new Zend_Validate_EmailAddress());

        return $element;
    }

    protected function _name()
    {
        $element = new Zend_Form_Element_Text('name');
        $element->setLabel('Name')
            ->addDecorators($this->_decorators)
            ->setRequired(true)
            ->setAttrib('class', 'span4');

        $element->addValidator(new Zend_Validate_Alnum(true));

        return $element;
    }
}