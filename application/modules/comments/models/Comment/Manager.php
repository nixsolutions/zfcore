<?php
/**
 * This is the Manager class for the comments table.
 *
 * @category Application
 * @package Comments
 * @subpackage Model
 *
 * @version  $Id: Comment.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_Model_Comment_Manager extends Core_Model_Manager
{
    /**
     * Get comments select by commentAlias row
     *
     * @param Comments_Model_CommentAlias $commentAlias
     * @return Zend_Db_Select
     */
    public function getSelect(Comments_Model_CommentAlias $commentAlias, $userId, $key = 0)
    {
        $users = new Users_Model_Users_Table();

        $select = $this->getDbTable()->select(true);
        $select->setIntegrityCheck(false)
            ->joinLeft(
                array(
                    'u' => $users->info('name')
                ), 
                'userId = u.id', 
                array('login', 'avatar', 'email', 'firstname', 'lastname')
            )
            ->where('aliasId = ?', $commentAlias->id)
            // select all "active" comments
            // and "not active" by the current user
            ->where(
                'comments.status = "' . Comments_Model_Comment::STATUS_ACTIVE . '"'
                . ' OR (comments.status != "' . Comments_Model_Comment::STATUS_ACTIVE . '"'
                . ' AND comments.userId = ?)', $userId);
        
        if ($commentAlias->isKeyRequired()) {
            $select->where('comments.key = ?', $key);
        }
        
        $select->order('created ASC');
        
        return $select;
    }
}