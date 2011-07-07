<?php
/**
 * Model Post
 *
 * @category Application
 * @package Model
 *
 * @version  $Id: Post.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Blog_Model_Post extends Core_Db_Table_Row_Abstract
{
    /** statuses */
    const STATUS_PUBLISHED  = 'published';
    const STATUS_DRAFT  = 'draft';
    const STATUS_DELETED = 'deleted';

    /**
     * @see Zend_Db_Table_Row_Abstract::_insert()
     */
    protected function _insert()
    {
        $this->created = date('Y-m-d H:i:s');

        if (!$this->userId) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            $this->userId = $identity->id;
        }

        $this->_update();
    }

    /**
     * @see Zend_Db_Table_Row_Abstract::_update()
     */
    protected function _update()
    {
        $this->updated = date('Y-m-d H:i:s');

        if (!$this->published) {
            $this->published = $this->updated;
        }
    }

    /**
     * Is user owner of the post
     *
     * @param object $identity
     * @return boolean
     */
    public function isOwner($identity = null)
    {
        if (!$identity) {
            $identity = Zend_Auth::getInstance()->getIdentity();
        }
        if ($identity) {
            return $this->userId == $identity->id;
        }
        return false;
    }
}