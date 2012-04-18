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
    protected $_uploadDir  = 'forum';

    /**
     * return upload dir
     *
     * @todo different folder for every user
     * @return string
     */
    protected function _getUploadDir()
    {
        return $this->_uploadDir;
    }
}