<?php
/**
 * Uploads images controller for forum module
 *
 * @category   Application
 * @package    Forum
 * @subpackage Controller
 */
class Forum_ImagesController extends Core_Controller_Action_Images
{
    /**
     * @var string
     */
    protected $_uploadDir  = 'forum';

    /**
     * return upload dir
     *
     * @throws Exception
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