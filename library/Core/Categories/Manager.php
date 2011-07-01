<?php
/**
 * Core_Category_Manager
 *
 * @version  $Id$
 */
abstract class Core_Categories_Manager extends Core_Model_Manager
{
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
     * Get root category
     *
     * @return Categories_Model_Categories_Row
     */
    abstract function getRoot();
}