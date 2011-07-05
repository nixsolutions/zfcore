<?php
/**
 * Blog_Model_Categories
 *
 * @version  $Id: Category.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Blog_Model_Categories
{
    const CATEGORY_ALIAS = 'blog';

    /**
     * @var Categories_Model_Categories
     */
    protected $_category;

    /**
     * Constructor
     */
    public function __construct()
    {
        $categories = new Categories_Model_Categories_Table();

        $this->_category = $categories->getByAlias(self::CATEGORY_ALIAS);
    }

    /**
     * Get all categories
     *
     * @param integer $down
     * @param string  $order
     * @param integer $limit
     * @param integer $offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getAll($down = null, $order = null, $limit = null,
        $offset = null)
    {
        return $this->_category->getAllChildren($down, $order, $limit, $offset);
    }

    /**
     * Get children categories
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getChildren()
    {
        return $this->_category->getChildren();
    }

    /**
     * Get by id
     *
     * @param integer $id
     */
    public function getById($id)
    {
        $categories = new Categories_Model_Categories_Table();

        $separator = Categories_Model_Categories::PATH_SEPARATOR;

        $select = $categories->select();
        $select->where('id=?', $id)
               ->where('alias LIKE ?', self::CATEGORY_ALIAS . $separator . '%');
        return $categories->fetchRow($select);
    }
}