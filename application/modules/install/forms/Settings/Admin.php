<?php
/**
 * Admin form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id$
 */
class Install_Form_Settings_Admin extends Zend_Form
{

    /**
     * Form initialization
     *
     * @return Users_Form_Users_Create
     */
    public function init()
    {
        $this->setName('userForm')->setMethod('post');

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
                 ->setRequired(true)
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
                ->setRequired(true)
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
                ->setRequired(true)
                ->addValidator('EmailAddress');

        return $element;
    }

    /**
     * Create submit element
     *
     * @return Zend_Form_Element_Submit
     */
    protected function _submit()
    {
        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel('Save & Next >');

        return $element;
    }
}
