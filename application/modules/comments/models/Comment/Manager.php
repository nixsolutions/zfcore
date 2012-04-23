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
     * @param                             $userId
     * @param int                         $key
     * @return Zend_Db_Select
     */
    public function getSelect(Comments_Model_CommentAlias $commentAlias, $userId, $key = 0)
    {
        $users = new Users_Model_User_Table();

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
                . ' AND comments.userId = ?)',
                $userId
            );
        
        if ($commentAlias->isKeyRequired()) {
            $select->where('comments.key = ?', $key);
        }
        
        $select->order('created ASC');
        
        return $select;
    }

    /**
     * Get the select for fetching comments
     *
     * @param Comments_Model_CommentAlias $commentAlias
     * @param int|string                  $userId
     * @return Zend_Db_Select
     */
    protected function _getCommentsAmountSelect(Comments_Model_CommentAlias $commentAlias, $userId = 0)
    {
        return $this->getDbTable()->select()
            ->setIntegrityCheck(false)
            ->from($this->getDbTable()->info('name'))
            ->where('aliasId = ?', $commentAlias->id)
            // select all "active" comments
            // and "not active" by the current user
            ->where(
                'comments.status = "' . Comments_Model_Comment::STATUS_ACTIVE . '"'
                . ' OR (comments.status != "' . Comments_Model_Comment::STATUS_ACTIVE . '"'
                . ' AND comments.userId = ?)',
                $userId
            )
            ->group('comments.aliasId');
    }

    /**
     * Fetch the amount of comments groupped by $fieldName
     * 
     * @param Comments_Model_CommentAlias $commentAlias
     * @param string $fieldName
     * @param array $fieldValues
     * @param integer $userId
     * @return array
     */
    public function getGrouppedCommentsAmount(
        Comments_Model_CommentAlias $commentAlias,
        $fieldName,
        array $fieldValues,
        $userId
    )
    {
        if (sizeof($fieldValues) < 1) {
            return array();
        }
        
        $tableFieldName = $commentAlias->relatedTable . '.' . $fieldName;
        
        // get select
        $select = $this->_getCommentsAmountSelect($commentAlias, $userId);
        
        // add specific columns: `commentsAmount` and field from
        $select->columns(
            array(
                $tableFieldName,
                'commentsAmount' => new Zend_Db_Expr('COUNT(*)')
            )
        );
        
        // join relatedTable which is declared in the admin for the CommentAlias
        if ($commentAlias->isRelatedTableDefined()) {
            $select->joinLeft(
                $commentAlias->relatedTable,
                $commentAlias->relatedTable . '.id = comments.key',
                array()
            );
        }
        
        $select->where($tableFieldName . ' IN (?)', $fieldValues)
            ->group($tableFieldName);

        $result = $this->getDbTable()->fetchAll($select);

        $itemComments = array();

        // group the result to the hash with $fieldName as key
        foreach ($result as $item) {
            if (!isset($itemComments[$item[$fieldName]])) {
                $itemComments[$item[$fieldName]] = 0;
            }
            $itemComments[$item[$fieldName]] += $item['commentsAmount'];
        }

        return $itemComments;
    }
}