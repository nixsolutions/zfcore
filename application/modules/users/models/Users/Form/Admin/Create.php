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
class Users_Model_Users_Form_Admin_Create extends Zend_Dojo_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('userForm')
             ->setMethod('post');

        $this->addElements(
            array($this->_login(),
                $this->_firstname(),
                $this->_lastname(),
                $this->_role(),
                $this->_status(),
                $this->_password(),
                $this->_email(),
                // $this->_date_create(),
                // $this->_date_login(),
                $this->_login_ip(),
                //  $this->_login_count(),
                $this->_submit())
        );
        return $this;
    }

    /**
     * Create user login element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _login()
    {
        $username = new Zend_Dojo_Form_Element_ValidationTextBox('login');
        $username->setLabel('Login')
                 ->setRequired(true)
                 ->setLowercase(true)
                 ->setTrim(true)
                 ->setAttribs(array('style'=>'width:60%'))
                 ->setMaxLength(Users_Model_User::MAX_USERNAME_LENGTH)
                 ->setRegExp('[a-z0-9]+')
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->addValidator('Alnum')
                 ->addValidator(
                     'StringLength', false,
                     array(Users_Model_User::MIN_USERNAME_LENGTH,
                           Users_Model_User::MAX_USERNAME_LENGTH)
                 )
                 ->addValidator(
                     'Db_NoRecordExists', false,
                     array(
                         array('table' => 'users',
                               'field' => 'login'))
                 );

        return $username;
    }

    /**
     * Create user first name element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _firstname()
    {
        $firstname = new Zend_Dojo_Form_Element_ValidationTextBox('firstname');
        $firstname->setLabel('First name')
                  ->setRequired(false)
                  ->setTrim(true)
                  ->setAttribs(array('style'=>'width:60%'))
                  ->setMaxLength(Users_Model_User::MAX_FIRSTNAME_LENGTH)
                  ->setRegExp('([a-zA-Z0-9 _-])+')
                  ->addFilter('StripTags')
                  ->addFilter('StringTrim')
                  ->addValidator('Alnum')
                  ->addValidator(
                      'StringLength', false,
                      array(0, Users_Model_User::MAX_FIRSTNAME_LENGTH)
                  );
        return $firstname;
    }

    /**
     * Create user last name element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _lastname()
    {
        $lastname = new Zend_Dojo_Form_Element_ValidationTextBox('lastname');
        $lastname->setLabel('Last name')
                 ->setRequired(false)
                 ->setTrim(true)
                 ->setMaxLength(Users_Model_User::MAX_LASTNAME_LENGTH)
                  ->setRegExp('([a-zA-Z0-9 _-])+')
                 ->setAttribs(array('style'=>'width:60%'))
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->addValidator('Alnum')
                 ->addValidator(
                     'StringLength', false,
                     array(0, Users_Model_User::MAX_LASTNAME_LENGTH)
                 );
         return $lastname;
    }

    /**
     * Create user role element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _role()
    {
        $role = new Zend_Dojo_Form_Element_ComboBox('role');
        $role->setLabel('Role')
             ->setAttribs(array('style'=>'width:60%'))
             ->setRequired(true)
             ->setMultiOptions(
                 array(
                     Users_Model_User::ROLE_GUEST =>
                        Users_Model_User::ROLE_GUEST,
                     Users_Model_User::ROLE_USER  =>
                        Users_Model_User::ROLE_USER,
                     Users_Model_User::ROLE_ADMIN =>
                        Users_Model_User::ROLE_ADMIN
                 )
             );
        return $role;
    }

    /**
     * Create user status element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _status()
    {
        $status = new Zend_Dojo_Form_Element_ComboBox('status');
        $status->setLabel('Status')
               ->setRequired(true)
               ->setAttribs(array('style' => 'width:60%'))
               ->setMultiOptions(
                   array(
                       Users_Model_User::STATUS_ACTIVE   =>
                           Users_Model_User::STATUS_ACTIVE,
                       Users_Model_User::STATUS_BLOCKED  =>
                           Users_Model_User::STATUS_BLOCKED,
                       Users_Model_User::STATUS_REGISTER =>
                           Users_Model_User::STATUS_REGISTER,
                       Users_Model_User::STATUS_REMOVED  =>
                           Users_Model_User::STATUS_REMOVED
                   )
               );

        return $status;
    }

    /**
     * Create user password element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _password()
    {
        $password = new Zend_Dojo_Form_Element_PasswordTextBox('password');
        $password->setLabel('Password')
                 ->setRequired(true)
                 ->setAttribs(array('style'=>'width:60%'))
                 ->setTrim(true)
                 ->setValue(null)
                 ->addValidator(
                     'StringLength', false,
                     array(Users_Model_User::MIN_PASSWORD_LENGTH)
                 );

        return $password;
    }

    /**
     * Create user email element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _email()
    {
        $email = new Zend_Dojo_Form_Element_ValidationTextBox('email');
        $email->setLabel('Email')
              ->setRequired(true)
              ->setAttribs(array('style'=>'width:60%'))
              ->setTrim(true)
              ->setRegExp('[\w\.\-\_\d]+\@[\w\.\-\_\d]+\.\w+')
              ->setValue(null)
              ->addValidator('StringLength', false, array(6))
              ->addValidator('EmailAddress')
              ->addValidator(
                  'Db_NoRecordExists',
                  false,
                  array(
                      array('table' => 'users', 'field' => 'email')
                  )
              );

        return $email;
    }

    /**
     * Create user date login element
     *
     * @return object Zend_Dojo_Form_Element_DateTextBox
     */
    protected function _date_login()
    {
        $date = new Zend_Dojo_Form_Element_DateTextBox('date_login');
        $date->setLabel('Date login')
             ->setDatePattern('yyyy-MM-dd HH:mm:ss')
             ->setAttribs(array('style'=>'width:60%;height:15px;'));

        return $date;
    }

    /**
     * Create user date create element
     *
     * @return object Zend_Dojo_Form_Element_DateTextBox
     */
    protected function _date_create()
    {
        $date = new Zend_Dojo_Form_Element_DateTextBox('date_create');
        $date->setLabel('Date create')
             ->setDatePattern('yyyy-MM-dd HH:mm:ss')
             ->setAttribs(array('style'=>'width:60%;height:15px;'));

        return $date;
    }

    /**
     * Create login ip element
     *
     * @return object Zend_Dojo_Form_Element_ValidationTextBox
     */
    protected function _login_ip()
    {
        $ip = new Zend_Dojo_Form_Element_ValidationTextBox('ip');
        $ip->setLabel('Login ip')
           ->setAttribs(array('style'=>'width:60%;'));

        return $ip;
    }

    /**
     * Create login count element
     *
     * @return object Zend_Dojo_Form_Element_NumberSpinner
     */
    protected function _login_count()
    {
        $ip = new Zend_Dojo_Form_Element_NumberSpinner('count');
        $ip->setLabel('Login count')
           ->setAttribs(array('style'=>'width:60%; height:15px;'));

        return $ip;
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
}