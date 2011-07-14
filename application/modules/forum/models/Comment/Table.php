<?php
/**
 * This is the Manager class for the forum_comment table.
 *
 * @category Application
 * @package Model
 * @subpackage Manager
 *
 * @author Ivan Nosov aka rewolf <i.k.nosov@gmail.com>
 *
 * @version  $Id: Manager.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Forum_Model_Comment_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'forum_comment';

    /** Primary Key */
    protected $_primary = 'id';

    /** Row Class */
    protected $_rowClass = 'Forum_Model_Comment';

    /**
     * Get comments for some post
     *
     * @param integer $postId
     * @return array
     */
    public function getCommentsSelect($postId)
    {
        $users = new Users_Model_Users_Table();

        $select = $this->select(true);
        $select->setIntegrityCheck(false)
               ->joinLeft(
            array('u' => $users->info('name')),
            'userId = u.id',
            array('login')
        )
        ->order('created')
        ->where('postId=?', $postId);

        return $select;
    }
}