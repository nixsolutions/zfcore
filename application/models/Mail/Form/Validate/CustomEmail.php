<?php
/**
 * Validates if email exists
 *
 * Required by register controller, forget password form
 * 
 * @category Application
 * @package Model
 * @subpackage Form
 * 
 * @version  $Id: CustomEmail.php 210 2010-11-23 12:17:03Z andreyalek $
 */
class Model_Mail_Form_Validate_CustomEmail extends Zend_Validate_Abstract
{
    const EXISTS = 'customEmail';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::EXISTS => 'Enter email'
    );

    /**
     * Usage: same as LoginNotExists
     *
     * @param string $value
     * @param string|array $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->_setValue((string) $value);
        $this->_error(self::EXISTS);
        
        if ($context['filter'] == 'custom email') {
            if (preg_match_all('/[\S]+\@[\S]+\.\w+/', $value, $matches)) {
                $mail = new Zend_Validate_EmailAddress();
                foreach ($matches['0'] as $value) {
                    if (!$mail->isValid($value)) {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }
        return true;
    }
}