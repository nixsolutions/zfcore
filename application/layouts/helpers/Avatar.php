<?php
/**
 * @category Application
 * @package  Core_View
 * @subpackage Helper
 *
 * @version  $Id$
 */
class Application_View_Helper_Avatar extends Zend_View_Helper_Abstract
{
    /**
     * Get current user avatar url
     *
     * @param string|Users_Model_User $image
     * @param string $email
     * @return string
     */
    public function avatar($image, $email = null)
    {
        if ($image instanceof Users_Model_User) {
            $email = $image->email;
            $image = $image->avatar;
        }
        if ($image) {
            return $this->view->baseUrl($image);
        }

        if ($email) {
            $email = trim($email);
            $email = strtolower($email);
            return 'http://www.gravatar.com/avatar/' . md5($email);
        }
    }
}
