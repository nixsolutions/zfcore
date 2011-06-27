<?php
/**
 * Category DBTable
 *
 * @category Application
 * @package Model
 * @subpackage Category
 *
 * @author Ivan Nosov aka rewolf <i.k.nosov@gmail.com>
 *
 * @version  $Id: Manager.php 163 2010-07-12 16:30:02Z AntonShevchuk $
 */
class Forum_Model_Category_Manager extends Core_Model_Manager
{
    const CATEGORY_ALIAS = 'forum';

    /**
     * @var Categories_Model_Categories_Row
     */
    protected $_category;

    /**
     * Constructor
     */
    public function __construct()
    {
        $categories = new Categories_Model_Categories_Table();
        $this->setDbTable($categories);

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
     * @return Zend_Db_Table_Row_Abstract
     */
    public function getById($id)
    {
        $separator = Categories_Model_Categories_Row::PATH_SEPARATOR;

        $select = $this->getDbTable()->select();
        $select->where('id=?', $id)
               ->where('alias LIKE ?', self::CATEGORY_ALIAS . $separator . '%');
        return $this->getDbTable()->fetchRow($select);
    }
}