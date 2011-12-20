<?php
/**
 * Model Comment
 *
 * @category Application
 * @package Comments
 * @subpackage Model
 * 
 * @version  $Id: Comment.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_Model_Comment extends Core_Db_Table_Row_Abstract
{
    const STATUS_ACTIVE = 'active';
    const STATUS_REVIEW = 'review';
    const STATUS_DELETED = 'deleted';
    
    /**
     * Magic method to set some row fields
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
     * Magic method to update some row fields
     *
     * @return void
     */
    public function _update()
    {
        $this->updated = date("Y-m-d h:i:s");
    }
}