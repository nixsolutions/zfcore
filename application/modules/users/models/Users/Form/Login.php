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
class Users_Model_Users_Form_Login extends Zend_Form
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
                 ->addFilter('StringTrim');

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
                 ->setRequired(true);

        $rememberMe = new Zend_Form_Element_Checkbox('rememberMe');
        $rememberMe->setLabel('Remember Me');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Login');

        $this->addElements(array($username, $password, $rememberMe, $submit));

        return $this;
    }
}