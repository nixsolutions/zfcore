<?php
/**
 * NewPassword form
 */
class Users_Model_Users_Form_NewPassword extends Zend_Form
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
        $passw->setRequired(true)
            ->setValue(null)
            ->addValidator(
                'StringLength',
                false,
                array(Users_Model_User::MIN_PASSWORD_LENGTH)
            );
        $passw->setDecorators(array('ViewHelper'));

        $passwAgain = new Zend_Form_Element_Password('passw_again');
        $passwAgain->setRequired(true)
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
        $passwAgain->setDecorators(array('ViewHelper'));

        $change = new Zend_Form_Element_Submit('change');
        $change->setLabel('Change');
        $change->setDecorators(array('ViewHelper'));

        $this->addElements(array($passw, $passwAgain, $change));

        return $this;
    }
}