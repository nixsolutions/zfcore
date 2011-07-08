<?php
/**
 * Model Comment
 *
 * @category Application
 * @package Model
 *
 * @author Ivan Nosov aka rewolf <i.k.nosov@gmail.com>
 *
 * @version  $Id: Comment.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Blog_Model_Comment extends Core_Db_Table_Row_Abstract
{
    /**
     * Allows pre-insert logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return  void
     */
    public function _insert()
    {
        $this->created = date("Y-m-d h:i:s");

        if (!$this->userId) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            $this->userId = $identity->id;
        }
        $this->_update();
    }

    /**
     * Allows pre-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    public function _update()
    {
        $this->updated = date("Y-m-d h:i:s");
    }
}