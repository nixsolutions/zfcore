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
     * @todo different folder for every user
     * @return string
     */
    protected function _getUploadDir()
    {
        return $this->_uploadDir;
    }
}