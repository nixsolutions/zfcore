<?php
/**
 * Post DBTable
 *
 * @category Application
 * @package Model
 * @subpackage Post
 *
 * @version  $Id: Manager.php 162 2010-07-12 14:58:58Z AntonShevchuk $
 */
class Blog_Model_Post_Manager extends Core_Model_Manager
{
    /**
     * get full info about post
     *
     * @param string $alias
     * @return Core_Db_Table_Row_Abstract
     */
    public function getPost($alias)
    {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(
                    array(
                        'p' => 'blog_post'
                    ),
                    array('*')
                )
                ->joinLeft(
                    array('u' => 'users'),
                    'p.userId = u.id',
                    array('login')
                )
                ->joinLeft(
                    array('c' => 'categories'),
                    'c.id = p.categoryId',
                    array('categoryTitle' => 'title', 'categoryAlias' => 'alias')
                )
                ->where('p.alias = ?', $alias);
        return $this->getDbTable()->fetchRow($select);
    }

    public function incrementCountView($postId)
    {
        $data = array(
            'views' => new Zend_Db_Expr('views + 1'),
        );
        $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $postId);
        $this->getDbTable()->update($data, $where);
    }

    public function getLastPostsSourse($idCat = null)
    {
        $select = $this->getDbTable()->select()->setIntegrityCheck(false)
                ->from(array('p' => 'blog_post'), array('*'))
                ->joinLeft(
                    array('u' => 'users'),
                    'p.userId = u.id',
                    array('login')
                )
                ->joinLeft(
                    array('c' => 'categories'),
                    'c.id = p.categoryId', array('categoryTitle' => 'title')
                )
                ->joinLeft(
                    array('com' => 'blog_comment'),
                    'p.id = com.postId',
                    array('comments' => new Zend_Db_Expr('COUNT(com.id)'))
                )
                ->group('p.id')
                ->order('p.created desc');
        if (!is_null($idCat)) {
            $select->where('p.categoryId = ?', $idCat);
        }
        return $select;
    }
}