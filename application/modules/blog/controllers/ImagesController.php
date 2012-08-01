<?php
/**
 * Uploads images controller for blog module
 *
 * @category   Application
 * @package    Blog
 * @subpackage Controller
 */
class Blog_ImagesController extends Core_Controller_Action_Images
{
    protected $_uploadDir  = 'blog';

    /**
     * return upload dir
     *
     * @return string
     */
    protected function _getUploadDir()
    {
        $user = Zend_Auth::getInstance()->getIdentity();
        if (!$user) {
            throw new Exception("Permissions denied");
        }

        return $this->_uploadDir .'/'. $user->id;
    }
}