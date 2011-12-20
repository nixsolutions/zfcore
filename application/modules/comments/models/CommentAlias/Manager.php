<?php
/**
 * This is the Manager class for the comments_aliases table.
 *
 * @category Application
 * @package Comments
 * @subpackage Model
 *
 * @version  $Id: Manager.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_Model_CommentAlias_Manager extends Core_Model_Manager
{
    /**
     * @var string
     */
    protected $_tableClass = 'Comments_Model_CommentAlias_Table';
    
    /**
     * Get by alias
     *
     * @param string $alias
     * @return Comments_Model_CommentAlias
     */
    public function getByAlias($alias)
    {
        $select = $this->getDbTable()
            ->select()
            ->where('alias = ?', $alias);
        return $this->getDbTable()->fetchRow($select);
    }
}