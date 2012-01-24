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
     * @param object|integer $category
     * @param integer $author
     * @param string  $date
     * @return Zend_Db_Table_Select
     */
    public function getSelect($category = null, $author = null, $date = 'NOW')
    {
        $users = new Users_Model_Users_Table();
        $categories = new Categories_Model_Category_Table();
        
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('p' => $this->_name), array('*'))
               ->joinLeft(
                   array('u' => $users->info('name')),
                   'p.userId = u.id',
                   array('login')
               )
               ->joinLeft(
                   array('c' => $categories->info('name')),
                   'c.id = p.categoryId',
                   array('categoryTitle' => 'title', 'categoryAlias' => 'alias')
               )
               ->group('p.id')
               ->where('p.status=?', Blog_Model_Post::STATUS_PUBLISHED)
               ->order('published DESC');

        if ($date) {
            if ('NOW' == $date) {
                $date = date('Y-m-d H:i:s');
            }
            $select->where('published <=?', $date);
        }
        if ($category) {
            if (!$category instanceof Zend_Db_Table_Row_Abstract) {
                $manager = new Blog_Model_Category_Manager();
                $category = $manager->getById($category);
            }
            $separator = Categories_Model_Category::PATH_SEPARATOR;
            $select->where('c.path LIKE ?', '%' . $category->alias . '%');
        }
        return $select;
    }
}