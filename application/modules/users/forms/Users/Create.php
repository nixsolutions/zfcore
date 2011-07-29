<?php
/**
 * Create user form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Create.php 163 2010-07-12 16:30:02Z AntonShevchuk $
 */
class Users_Form_Users_Create extends Zend_Form
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
        $this->addElement($this->_firstname());
        $this->addElement($this->_lastname());
        $this->addElement($this->_role());
        $this->addElement($this->_status());
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
                 ->addValidator(
                     'Db_NoRecordExists',
                     false,
                     array(
                         array('table' => 'users', 'field' => 'login')
                     )
                 );

        return $element;
    }

    /**
     * Create user first name element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _firstname()
    {
        $element = new Zend_Form_Element_Text('firstname');
        $element->setLabel('First name')
                ->setRequired(false)
                ->addFilter('StringTrim')
                ->addValidator('Alnum')
                ->addValidator(
                    'StringLength',
                    false,
                    array("max" => Users_Model_User::MAX_FIRSTNAME_LENGTH)
                );
        return $element;
    }

    /**
     * Create user last name element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _lastname()
    {
        $element = new Zend_Form_Element_Text('lastname');
        $element->setLabel('Last name')
                ->setRequired(false)
                ->addFilter('StringTrim')
                ->addValidator('Alnum')
                ->addValidator(
                    'StringLength',
                    false,
                    array('max' => Users_Model_User::MAX_LASTNAME_LENGTH)
                );
         return $element;
    }

    /**
     * Create user role element
     *
     * @return Zend_Form_Element_Multiselect
     */
    protected function _role()
    {
        $element = new Zend_Form_Element_Select('role');
        $element->setLabel('Role')
                ->setRequired(true);

        $element->addMultiOption(Users_Model_User::ROLE_USER, 'User');
        $element->addMultiOption(Users_Model_User::ROLE_ADMIN, 'Admin');

        return $element;
    }

    /**
     * Create user status element
     *
     * @return Zend_Form_Element_Multiselect
     */
    protected function _status()
    {
        $element = new Zend_Form_Element_Select('status');
        $element->setLabel('Status')
                ->setRequired(true);

        $element->addMultiOption(Users_Model_User::STATUS_ACTIVE, 'Active');
        $element->addMultiOption(Users_Model_User::STATUS_BLOCKED, 'Blocked');
        $element->addMultiOption(Users_Model_User::STATUS_REGISTER, 'Registered');
        $element->addMultiOption(Users_Model_User::STATUS_REMOVED, 'Removed');

        return $element;
    }

    /**
     * Create user password element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _password()
    {
        $element = new Zend_Form_Element_Text('password');
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
                ->addValidator('EmailAddress')
                ->addValidator(
                    'Db_NoRecordExists',
                    false,
                    array(
                        array('table' => 'users', 'field' => 'email')
                    )
                );

        return $element;
    }

    /**
     * Create user date login element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _logined()
    {
        $element = new Zend_Form_Element_Text('date_login');
        $element->setLabel('Date login')
                ->addValidator(new Zend_Validate_Date('Y-m-d H:i:s'));
        return $element;
    }

    /**
     * Create user date create element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _created()
    {
        $element = new Zend_Form_Element_Text('date_create');
        $element->setLabel('Date create')
                ->addValidator(new Zend_Validate_Date('Y-m-d H:i:s'));

        return $element;
    }

    /**
     * Create login ip element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _ip()
    {
        $element = new Zend_Form_Element_Text('ip');
        $element->setLabel('Login ip')
                ->addValidator(new Zend_Validate_Ip());

        return $element;
    }

    /**
     * Create login count element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _count()
    {
        $element = new Zend_Form_Element_Text('count');
        $element->setLabel('Login count');

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
        $element->setLabel('Save');

        return $element;
    }
}