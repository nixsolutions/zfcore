<?php
/**
 * ApiController for users module
 *
 * @category   Application
 * @package    Users
 * @subpackage Controller
 */
class Users_ApiController extends Core_Controller_Action
{
    /**
     * User profile for Vanilla Forum
     * @see http://vanillaforums.org/page/ProxyConnect_SSO
     */
    public function proxyAction()
    {
        // disable View
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // get current User
        $user = Zend_Auth::getInstance()->getIdentity();
        if ($user) {
            echo "UniqueID={$user->id}\n"
                 . "Name={$user->username}\n"
                 . "Email={$user->email}\n"
                 . "Roles={$user->role}\n";
        } else {
            echo "UniqueID=\n"
                 . "Name=\n"
                 . "Email=\n";
        }
    }
}



