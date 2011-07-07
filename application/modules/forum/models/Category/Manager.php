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
     * @var Categories_Model_Categories
     */
    protected $_root;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDbTable(new Categories_Model_Categories_Table());
    }

    /**
     * Get Forum root category
     *
     * @return Categories_Model_Categories
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
        $separator = Categories_Model_Categories::PATH_SEPARATOR;

        $select = $this->getDbTable()->select();
        $select->where('id=?', $id)
               ->where('path LIKE ?', self::CATEGORY_ALIAS . $separator . '%');
        return $this->getDbTable()->fetchRow($select);
    }
}