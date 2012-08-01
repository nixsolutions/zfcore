<?php
/**
 * Admin form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Install_Form_Settings_Admin extends Core_Form
{

    /**
     * Form initialization
     *
     * @return Install_Form_Settings_Admin
     */
    public function init()
    {
        $this->setName('adminForm')->setMethod('post');

        $this->addElement($this->_login());
        $this->addElement($this->_password());
        $this->addElement($this->_email());
        $this->addElement($this->_submit());

        return $this;
    }

    /**
     * Create user login element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _login()
    {
        $element = new Zend_Form_Element_Text('login');
        $element->setLabel('Login')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4')
                ->addFilter('StringTrim')
                ->addValidator('Alnum')
                ->addValidator(
                    'StringLength',
                    false,
                    array(Users_Model_User::MIN_USERNAME_LENGTH,
                        Users_Model_User::MAX_USERNAME_LENGTH)
                )
                ->setValue('admin');

        return $element;
    }


    /**
     * Create user password element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _password()
    {
        $element = new Zend_Form_Element_Password('password');
        $element->setLabel('Password')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4')
                ->addValidator(
                    'StringLength',
                    false,
                    array('min' => Users_Model_User::MIN_PASSWORD_LENGTH)
                );

        return $element;
    }

    /**
     * Create user email element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _email()
    {
        $element = new Zend_Form_Element_Text('email');
        $element->setLabel('Email')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4')
                ->addValidator('EmailAddress');

        return $element;
    }
}
