<?php
/**
 * Model Post
 *
 * @category Application
 * @package Model
 *
 * @version  $Id: Post.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Forum_Model_Post extends Core_Db_Table_Row_Abstract
{
    /** statuses */
    const STATUS_ACTIVE  = 'active';
    const STATUS_CLOSED  = 'closed';
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

    /**
     * Inc views
     *
     * @param integer $count
     * @return Forum_Model_Post
     */
    public function incViews($count = 1)
    {
        $session = new Zend_Session_Namespace("Forum_Posts");
        if (!isset($session->views[$this->id])) {
            if ($this->isReadOnly()) {
                $row = $this->getTable()->find($this->_getPrimaryKey());
            } else {
                $row = $this;
            }
            $row->views += $count;
            $row->save();

            if (!is_array($session->views)) {
                $session->views = array();
            }
            $session->views[$this->id] = true;
        }
        return $this;
    }

    /**
     * Inc comments
     *
     * @param integer $count
     * @return Forum_Model_Post
     */
    public function incComments($count = 1)
    {
        if ($this->isReadOnly()) {
            $row = $this->getTable()->find($this->_getPrimaryKey());
        } else {
            $row = $this;
        }
        $row->comments += $count;
        $row->save();

        return $this;
    }

    /**
     * Get teaser
     *
     * @param integer $length
     * @return string
     */
    public function getTeaser($length = 250)
    {
        $teaser = strip_tags($this->body);
        return substr($teaser, 0, $length);
    }
}