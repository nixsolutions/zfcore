<?php
/**
 * Categories_Model_Categories_Rowset
 *
 * @version $Id$
 */
class Categories_Model_Categories_Rowset extends Zend_Db_Table_Rowset_Abstract
{
    /**
     * Add row
     *
     * @param Categories_Model_Categories_Row $row
     * @return Categories_Model_Categories_Rowset
     */
    public function addRow(Categories_Model_Categories_Row $row)
    {
        $this->_data[] = $row->toArray();
        if ($this->_count == count($this->_rows)) {
            $this->_rows[] = $row;
        }
        // set the count of rows
        $this->_count = count($this->_data);

        return $this;
    }
}