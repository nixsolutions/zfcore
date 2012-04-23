<?php
/**
 * Helper_DataTables
 */
class Helper_DataTables
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Get DataTable data
     *
     * @param Zend_Db_Table_Select $select
     * @param array|null $columns
     * @return array
     */
    public function direct(Zend_Db_Table_Select $select, $columns = null)
    {
        $table = $select->getTable();
        if (!$columns) {
            $columns = $table->info(Zend_Db_Table_Abstract::COLS);
        }

        $colCount = count($columns);
        $request = $this->getRequest();

        /*
         * Ordering
         */
        if (null !== $request->getParam('iSortCol_0', null)) {
            for ($i = 0, $l = $request->getParam('iSortingCols'); $i < $l; $i++) {
                if ($request->getParam('bSortable_'.(int)$request->getParam('iSortCol_'.$i))) {
                    $select->order(
                        $columns[(int)$request->getParam('iSortCol_'.$i)]
                        . " " .  $request->getParam('sSortDir_' . $i)
                    );
                }
            }
        }

        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        if ($search = $request->getParam('sSearch')) {
            for ($i = 0; $i < $colCount; $i++) {
                $select->orHaving("{$columns[$i]} LIKE ?", '%' . $search . '%');
            }
        }

        /* Individual column filtering */
        for ($i = 0; $i < $colCount; $i++) {
            if ($request->getParam('bSearchable_' . $i) == "true") {
                if ($search = $request->getParam('sSearch_' . $i)) {
                    $select->having("{$columns[$i]} LIKE ?", '%' . $search . '%');
                }
            }
        }
        //save current query for fetching data
        $query = clone $select;
        $tableName = $table->info(Zend_Db_Table::NAME);
        $expr = new Zend_Db_Expr('COUNT(*) as total');

        /* Data set length after filtering */
        if ($select->getPart(Zend_Db_Select::FROM)) {
            $select->columns($expr);
        } else {
            $select->from($tableName, $expr);
        }
        $iFilteredTotal = $table->fetchRow($select)->total;

        $query->limit(
            $request->getParam('iDisplayLength'),
            $request->getParam('iDisplayStart')
        );

        /*
         * SQL queries
         * Get data to display
         */
        if ($query->getPart(Zend_Db_Select::FROM)) {
            $query->columns($columns);
        } else {
            $query->from($tableName, $columns);
        }

        // Get total rows count
        $select = $table->select()->from($tableName, $expr);
        $iTotalRecords = $table->fetchRow($select)->total;

        return array(
            "sEcho" => (int) $request->getParam('sEcho'),
            "aaData" => $table->fetchAll($query)->toArray(),
            "iTotalRecords" => $iTotalRecords,
            "iTotalDisplayRecords" => $iFilteredTotal
        );
    }
}