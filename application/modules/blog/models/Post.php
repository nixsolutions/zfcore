<?php
/**
 * Model Post
 *
 * @category Application
 * @package Model
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

        if (!$this->alias) {
            $this->alias = $this->title;
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

        if (!empty($this->_modifiedFields['alias'])) {
            $this->alias = preg_replace('|(\W+)|uim ', '-', $this->alias);
            $this->alias = strtolower($this->alias);
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

    /**
     * Inc views
     *
     * @param integer $count
     * @return Blog_Model_Post
     */
    public function incViews($count = 1)
    {
        $session = new Zend_Session_Namespace("Blog_Posts");
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
}