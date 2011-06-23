<?php
/**
 * Login form
 * 
 * @category Application
 * @package Model
 * @subpackage Form
 * 
 * @version  $Id: Login.php 163 2010-07-12 16:30:02Z AntonShevchuk $
 */
class Model_User_Form_Login extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('userLoginForm');

        $username = new Zend_Form_Element_Text('login');
        $username->setLabel('User name')
                 ->setRequired(true)
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->addValidator('Alnum')
                 ->addValidator(
                     'StringLength', false,
                     array(Users_Model_User::MIN_USERNAME_LENGTH,
                           Users_Model_User::MAX_USERNAME_LENGTH)
                 );

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
                 ->setRequired(true)
                 ->setValue(null)
                 ->addValidator(
                     'StringLength', false,
                     array(Users_Model_User::MIN_PASSWORD_LENGTH)
                 );

        $rememberMe = new Zend_Form_Element_Checkbox('rememberMe');
        $rememberMe->setLabel('Remember Me');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Login');

        $this->addElements(array($username, $password, $rememberMe, $submit));

        return $this;
    }
}