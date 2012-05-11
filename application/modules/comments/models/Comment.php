<?php
/**
 * Model Comment
 *
 * @category Application
 * @package Comments
 * @subpackage Model
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
        
        $this->incComments();
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

    /**
     *
     */
    public function _delete()
    {   
        $this->decComments();
    }

    /**
     * Increment comments amount
     *
     * @param integer $count
     * @throws Zend_Db_Exception
     * @return Comments_Model_CommentAlias
     */
    protected function incComments($count = 1)
    {
        $aliasManager = new Comments_Model_CommentAlias_Manager();
        $alias = $aliasManager->getDbTable()->find($this->aliasId)->current();
        
        if ($alias->isKeyRequired() && $alias->isRelatedTableDefined()) {
            $table = new Zend_Db_Table($alias->relatedTable);
            $row = $table->find($this->key)->current();
            
            if ($row) {
                $row->comments += $count;
                $row->save();
            } else {
                throw new Zend_Db_Exception('Row not found');
            }
        }
        
        return $this;
    }

    /**
     * Decrement comments amount
     *
     * @param integer $count
     * @throws Zend_Db_Exception
     * @return Comments_Model_CommentAlias
     */
    protected function decComments($count = 1)
    {
        $aliasManager = new Comments_Model_CommentAlias_Manager();
        $alias = $aliasManager->getDbTable()->find($this->aliasId)->current();
        
        if ($alias->isKeyRequired() && $alias->isRelatedTableDefined()) {
            $table = new Zend_Db_Table($alias->relatedTable);
            $row = $table->find($this->key)->current();
            
            if ($row) {
                $row->comments -= $count;
                $row->save();
            } else {
                throw new Zend_Db_Exception('Row not found');
            }
        }
        
        return $this;
    }
    
    /**
     * Concat first and last name
     * 
     * @return string
     */
    public function getUserName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}