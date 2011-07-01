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
class Blog_Model_Category_Manager extends Core_Model_Manager
{
    const CATEGORY_ALIAS = 'blog';

    /**
     * @var Categories_Model_Categories_Row
     */
    protected $_category;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDbTable(new Categories_Model_Categories_Table());
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
        return $this->getRoot()->getAllChildren($down, $order, $limit, $offset);
    }

    /**
     * Get children categories
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getChildren()
    {
        return $this->getRoot()->getChildren();
    }

    /**
     * Get blog root category
     *
     * @return Categories_Model_Categories_Row
     */
    public function getRoot()
    {
        if (!$this->_category) {
            $this->_category = $this->getDbTable()->getByAlias(self::CATEGORY_ALIAS);
        }
        return $this->_category;
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