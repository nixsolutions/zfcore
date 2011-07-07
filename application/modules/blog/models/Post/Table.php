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
class Blog_Model_Post_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'blog_post';

    /** Primary Key */
    protected $_primary = 'id';

    /** Row Class */
    protected $_rowClass = 'Blog_Model_Post';

    /**
     * Get Zend_Db_Table_Select
     *
     * @param string $alias
     * @return Zend_Db_Table_Select
     */
    public function getPostsSourse($alias = null)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('p' => 'blog_post'), array('*'))
        ->joinLeft(
            array('u' => 'users'),
                'p.userId = u.id', array('login')
        )
        ->joinLeft(
            array('c' => 'categories'),
            'c.id = p.categoryId',
            array('categoryTitle' => 'title', 'categoryAlias' => 'alias')
        )
        ->joinLeft(
            array('com' => 'blog_comment'),
            'p.id = com.postId',
            array('comments' => new Zend_Db_Expr('COUNT(com.id)'))
        )->group('p.id')
        ->where('p.status=?', Blog_Model_Post::STATUS_PUBLISHED);

        if ($alias) {
            $separator = Categories_Model_Categories::PATH_SEPARATOR;
            $select->where('c.path LIKE ?', '%' . $alias . '%');
        }
        return $select;
    }
}