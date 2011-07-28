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
class Users_Model_Users_Form_Profile extends Users_Model_Users_Form_Register
{
    /**
     * @var Users_Model_User
     */
    protected $_row;

    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('userForm');

        $this->addElement($this->_firstname());
        $this->addElement($this->_lastname());
        $this->addElement($this->_avatar());
        $this->addElement($this->_email());
        $this->addElement($this->_newPassword());
        $this->addElement($this->_confirmPassword());
        $this->addElement($this->_submit());

        return $this;
    }

    /**
     * Confirm password
     *
     * @return boolen
     */
    public function confirmPassword($value, $context)
    {
        return $value === $context['confirmPassword'];
    }

    /**
     * Set user row
     *
     * @param Users_Model_User $row
     * @return Users_Model_Users_Form_Profile
     */
    public function setUser(Users_Model_User $row)
    {
        if ($row->password) {
            $this->addElement($this->_currentPassword($row));
            $this->getElement('submit')->setOrder(100);
        }
        $this->getElement('email')->getValidator('Db_NoRecordExists')
                                  ->setExclude(array('field' => 'id', 'value' => $row->id));

        $this->_row = $row;

        return parent::setDefaults($row->toArray());
    }

    /**
     * @see Zend_Form::isValid()
     */
    public function isValid($data)
    {
        if ($currentPassword = $this->getElement('currentPassword')) {
            if (!$this->_row->isEmail($data['email'])) {
                $currentPassword->setRequired(true);
            } elseif (!empty($data['password'])) {
                $currentPassword->setRequired(true);
            }
        }

        return parent::isValid($data);
    }

    /**
     * Get firstname element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _firstname()
    {
        $element = new Zend_Form_Element_Text('firstname');
        $element->setLabel('First Name')
                 ->addValidator(
                     'StringLength',
                     false,
                     array('max' => Users_Model_User::MAX_FIRSTNAME_LENGTH)
                 )
                 ->addValidator('Alpha');

        return $element;
    }

    /**
     * Get lastname element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _lastname()
    {
        $element = new Zend_Form_Element_Text('lastname');
        $element->setLabel('Last Name')
                 ->addValidator(
                     'StringLength',
                     false,
                     array('max' => Users_Model_User::MAX_LASTNAME_LENGTH)
                 )
                 ->addValidator('Alpha');

        return $element;
    }

    /**
     * Get avatar element
     *
     * @return Zend_Form_Element_Password
     */
    protected function _avatar()
    {
        $element = new Zend_Form_Element_File('avatar');
        $element->setLabel('Last Name')
                ->addValidator('isImage')
                ->addFilter(new Zend_Filter_File_Rename(array('target' => md5(time()))))
                ->addFilter(new Users_Model_Users_Form_Filter_ImageSize(80, 80))
                ->setDestination(APPLICATION_PATH . '/../public/uploads');

        return $element;
    }


    /**
     * Get new password element
     *
     * @return Zend_Form_Element_Password
     */
    protected function _newPassword()
    {
        $callback = new Zend_Validate_Callback(array($this, 'confirmPassword'));
        $callback->setMessage('Error Confirm Password');

        $element = new Zend_Form_Element_Password('password');
        $element->setLabel('New Password')
                ->addValidator(
                    'StringLength',
                    false,
                    array(Users_Model_User::MIN_PASSWORD_LENGTH)
                )
                ->addValidator($callback);

        return $element;
    }

    /**
     * Get new password element
     *
     * @return Zend_Form_Element_Password
     */
    protected function _confirmPassword()
    {
        $element = new Zend_Form_Element_Password('confirmPassword');
        $element->setLabel('Confirm New Password');

        return $element;
    }

    /**
     * Get old password element
     *
     * @return Zend_Form_Element_Password
     */
    protected function _currentPassword(Users_Model_User $row)
    {
        $callback = new Zend_Validate_Callback(array($row, 'isPassword'));
        $callback->setMessage('Error Current Password');

        $element = new Zend_Form_Element_Password('currentPassword');
        $element->setLabel('Current Password')
                ->addValidator($callback);

        $this->getElement('email')->setDescription('Requires current password');
        $this->getElement('password')->setDescription('Requires current password');
        return $element;
    }

    /**
     * Get email element
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
                    'Db_NoRecordExists', false,
                    array(
                        array('table' => 'users', 'field' => 'email')
                    )
                );

        return $element;
    }

    /**
     * Get submit button
     *
     * @return Zend_Form_Element_Submit
     */
    protected function _submit()
    {
        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel('Update');

        return $element;
    }
}