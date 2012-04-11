<?php
/**
 * NewPassword form
 *
 * @category Application
 * @package Form
 * @subpackage Users
 */
class Users_Form_Auth_NewPassword extends Core_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('userNewPasswordForm');

        $passw = new Zend_Form_Element_Password('passw');
        $passw->setLabel('Password')
            ->addDecorators($this->_inputDecorators)
            ->setRequired(true)
            ->setValue(null)
            ->addValidator(
                'StringLength',
                false,
                array(Users_Model_User::MIN_PASSWORD_LENGTH)
            );

        $passwAgain = new Zend_Form_Element_Password('passw_again');
        $passwAgain->setLabel('Password again')
            ->addDecorators($this->_inputDecorators)
            ->setRequired(true)
            ->setValue(null)
            ->setValidators(
                array(
                    array('StringLength', false, array(
                        'min' => Users_Model_User::MIN_PASSWORD_LENGTH,
                        'max' => '50'
                    )),
                    array('Identical', false, array('token' => 'passw'))
                )
            );

        $change = new Zend_Form_Element_Submit('change');
        $change->setLabel('Change');
        $change->setAttrib('class', 'btn btn-primary');

        $this->addElements(array($passw, $passwAgain, $change));

        return $this;
    }
}