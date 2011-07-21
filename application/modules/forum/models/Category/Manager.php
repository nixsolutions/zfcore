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
class Forum_Model_Category_Manager extends Core_Categories_Manager
{
    const CATEGORY_ALIAS = 'forum';

    /**
     * @var Categories_Model_Category
     */
    protected $_root;

    /**
     * @var string
     */
    protected $_tableClass = 'Categories_Model_Category_Table';

    /**
     * Constructor
     *
     * @param Zend_Db_Table $table
     */
    public function __construct(Zend_Db_Table_Abstract $table = null)
    {
        if (!$table) {
            $table = new $this->_tableClass;
        }
        $this->setDbTable($table);
    }

    /**
     * Get Forum root category
     *
     * @return Categories_Model_Category
     */
    public function getRoot()
    {
        if (!$this->_root) {
            $this->_root = $this->getDbTable()->getByAlias(self::CATEGORY_ALIAS);
        }
        return $this->_root;
    }

    /**
     * Get by id
     *
     * @param integer $id
     * @return Zend_Db_Table_Row_Abstract
     */
    public function getById($id)
    {
        $separator = Categories_Model_Category::PATH_SEPARATOR;

        $select = $this->getDbTable()->select();
        $select->where('id=?', $id)
               ->where('path LIKE ?', self::CATEGORY_ALIAS . $separator . '%');
        return $this->getDbTable()->fetchRow($select);
    }

    /**
     * Get by alias
     *
     * @param string $alias
     * @return Zend_Db_Table_Row_Abstract
     */
    public function getByAlias($alias)
    {
        $separator = Categories_Model_Category::PATH_SEPARATOR;

        $select = $this->getDbTable()->select();
        $select->where('alias=?', $alias)
               ->where('path LIKE ?', self::CATEGORY_ALIAS . $separator . '%');
        return $this->getDbTable()->fetchRow($select);
    }
}