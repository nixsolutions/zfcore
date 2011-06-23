<?php
/**
 * Register user form
 * 
 * @category Application
 * @package Model
 * @subpackage Form
 * 
 * @todo Refactoring with DB validators 
 * http://framework.zend.com/manual/en/zend.validate.set.html
 * #zend.validate.db.excluding-records
 * 
 * @version  $Id: Register.php 153 2010-07-08 11:51:49Z AntonShevchuk $
 */
class Users_Model_Users_Form_Register extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->addElementPrefixPath(
            'Users_Model_Users_Form_Validate',
            APPLICATION_PATH . "/modules/users/models/Users/Form/Validate",
            'validate'
        );

        $this->setName('userRegisterForm');

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
                 )
                 ->addValidator(
                     'Db_NoRecordExists', false,
                     array(
                         array('table' => 'users',
                               'field' => 'login')
                     )
                 );


        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
                 ->setRequired(true)
                 ->setValue(null)
                 ->addValidator(
                     'StringLength', false,
                     array(Users_Model_User::MIN_PASSWORD_LENGTH)
                 )
                 ->addValidator('PasswordConfirmation');


        $confirmPassword = new Zend_Form_Element_Password('password2');
        $confirmPassword->setLabel('Password again')
                  ->setRequired(true)
                  ->setValue(null)
                  ->addValidator(
                      'StringLength', false,
                      array(Users_Model_User::MIN_PASSWORD_LENGTH)
                  );

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email')
              ->setRequired(true)
              ->setValue(null)
              ->addValidator('StringLength', false, array(6))
              ->addValidator('EmailAddress')
              ->addValidator(
                  'Db_NoRecordExists', false,
                  array(
                      array('table' => 'users', 'field' => 'email')
                  )
              );

        $imgDir = dirname(APPLICATION_PATH) . "/public/images/captcha";

        // check captcha path is writeable        
        if (is_writable($imgDir)) {
            $captcha = new Zend_Form_Element_Captcha(
                'captcha',
                array(
                    'label' => "Please verify you're a human",
                    'captcha' => 'Image',
                    'captchaOptions' => array(
                        'captcha' => 'Image',
                        'wordLen' => 6,
                        'timeout' => 300,
                        'imgDir' => $imgDir,
                        'font' => dirname(APPLICATION_PATH) . 
                                  "/data/fonts/Aksent_Normal.ttf",
                    ),
                )
            );
        } else {
            $captcha = new Zend_Form_Element_Captcha(
                'captcha',
                array(
                    'label' => "Please verify you're a human",
                    'captcha' => 'Figlet',
                    'captchaOptions' => array(
                        'wordLen' => 6,
                        'timeout' => 300,
                    ),
                )
            );
        }
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Register');

        $this->addElements(
            array(
                $username,
                $password,
                $confirmPassword,
                $email,
                $captcha,
                $submit
            )
        );

        return $this;
    }
}