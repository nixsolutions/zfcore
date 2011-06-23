<?php
/**
 * Forget password form
 * 
 * @category Application
 * @package Model
 * @subpackage Form
 * 
 * @version  $Id: Forget.php 160 2010-07-12 10:47:54Z AntonShevchuk $
 */
class Users_Model_Users_Form_Forget extends Zend_Form
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

        $this->setName('userForgetPasswordForm');
        
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email')
              ->setRequired(true)
              ->setValue(null)
              ->addValidator('StringLength', false, array(6))
              ->addValidator('EmailAddress')
              ->addValidator(
                  'Db_RecordExists',
                  false,
                  array(
                      array('table' => 'users', 'field' => 'email')
                  )
              );

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Restore');

        $this->addElements(array($email, $submit));

        return $this;
    }
}