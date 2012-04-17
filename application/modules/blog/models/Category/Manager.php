<?php
/**
 * Blog_Model_Category_Manager
 *
 * @version $Id$
 */
class Blog_Model_Category_Manager extends Core_Categories_Manager
{
    const CATEGORY_ALIAS = 'blog';

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
     * Get blog root category
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

    /**
     * Get categories list
     *
     * @param boolean $fetch
     * @return Zend_Db_Table_Select|Zend_Db_Table_Rowset
     */
    public function getList($fetch = false)
    {
        $separator = Categories_Model_Category::PATH_SEPARATOR;

        $select = $this->getDbTable()->select();

        $select->where('path LIKE ?', self::CATEGORY_ALIAS . $separator . '%')
               ->order('path');

        if (!$fetch) {
            return $select;
        }
        return $this->getDbTable()->fetchAll($select);
    }
}