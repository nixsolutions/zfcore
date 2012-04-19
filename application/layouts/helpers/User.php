<?php
/**
 * @category Application
 * @package  Core_View
 * @subpackage Helper
 */
class Application_View_Helper_User extends Zend_View_Helper_Abstract
{
    /**
     * Get current user identity
     *
     * @return Users_Model_User|null
     */
    public function user()
    {
        return Zend_Auth::getInstance()->getIdentity();
    }
}
