<?php
/**
 * Validates if passwords matches
 *
 * Required by register controller, register form
 * 
 * @category Application
 * @package Model
 * @subpackage Form
 * 
 * @version  $Id: PasswordConfirmation.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Model_User_Form_Validate_PasswordConfirmation 
    extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Password confirmation does not match'
    );

    /**
     * USAGE: see LoginNotExists validator documentation
     *
     * @param string $value
     * @param string|array $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context)) {
            if (isset($context['password2'])
                && ($value == $context['password2'])) {
                return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
}