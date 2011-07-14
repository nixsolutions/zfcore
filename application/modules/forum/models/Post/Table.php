<?php
/**
 * Post DBTable
 *
 * @category Application
 * @package Model
 * @subpackage Post
 *
 * @version  $Id: Manager.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Forum_Model_Post_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'forum_post';

    /** Primary Key */
    protected $_primary = 'id';

    /** Row Class */
    protected $_rowClass = 'Forum_Model_Post';

    /**
     * Get last posts for categories
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getLastPosts()
    {
        $users = new Users_Model_Users_Table();

        $select = $this->select()->from(array('p' => $this->_name), array('*'));
        $select->setIntegrityCheck(false)
               ->joinLeft(
            array('u' => $users->info('name')),
            'userId=u.id',
            array('login')
        );
        $select->order('created DESC');
        $select->group('categoryId');
        $select->where('p.status=?', Forum_Model_Post::STATUS_ACTIVE);
        $select->columns(
            array(
                'postsCount' => new Zend_Db_Expr('COUNT(p.id)'),
                'viewsCount' => new Zend_Db_Expr('SUM(views)'),
                'commentsCount' => new Zend_Db_Expr('SUM(comments)'),
            )
        );

        $rows = array();
        foreach ($this->fetchAll($select) as $row) {
            $rows[$row->categoryId] = $row;
        }
        return $rows;
    }

    /**
     * Get posts
     *
     * @param integer $categoryId
     * @return Zend_Db_Table_Select
     */
    public function getPostsSelect($categoryId = null)
    {
        $users = new Users_Model_Users_Table();
        $comments = new Forum_Model_Comment_Table();

        $select = $this->select()->from(array('p' => $this->_name), array('*'));
        $select->setIntegrityCheck(false)
            ->joinLeft(
            array('u' => $users->info('name')),
            'userId=u.id',
            array('author' =>'login')
        )->joinLeft(
            array('c' => $comments->info('name')),
            'p.id=c.postId',
            array('commentCreated' =>'created')
        )->joinLeft(
            array('u2' => $users->info('name')),
            'c.userId=u2.id',
            array('commentAuthor' =>'login')
        );
        $select->order(array('p.created DESC', 'c.created DESC'));
        $select->group('p.id');
        $select->where('p.status=?', Forum_Model_Post::STATUS_ACTIVE);

        if ($categoryId) {
            $select->where('categoryId=?', $categoryId);
        }
        return $select;
    }
}