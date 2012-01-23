<?php
/**
 * Created by glide/malinovskiy.
 * Based on dbDiff.class.php (max.antonoff@gmail.com)
 * Date: 20.01.12
 * Time: 17:09
 */

class Core_Migration_Db_Diff
{
    /**
     * @var Core_Migration_Db
     */
    protected $_currentDb;
    /**
     * @var Core_Migration_Db
     */
    protected $_publishedDb;

    /**
     * @var array
     */
    protected $_difference = array('up' => array(), 'down' => array());

    protected $_createTables = array();
    protected $_dropTables = array();
    protected $_commonTables = array();

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;


    public function __construct($currentDb, $lastPublishedDb)
    {
        $this->_currentDb = $currentDb;
        $this->_publishedDb = $lastPublishedDb;

        $this->_db = Zend_Db_Table::getDefaultAdapter();

    }

    protected function up($sql)
    {
        if (!strlen($sql)) return;
        $this->_difference['up'][] = $sql;
    }

    protected function down($sql)
    {
        if (!strlen($sql)) return;
        $this->_difference['down'][] = $sql;
    }

    public function getDifference()
    {
        $this->compareTables();

        $this->compareCommonTablesScheme();

        return $this->_difference;
    }

    protected function compareTables()
    {
        $currentTables = $this->_currentDb->getTables();
        $lastPublishedTables = $this->_publishedDb->getTables();

        $this->_createTables = array_diff_key($currentTables, $lastPublishedTables);
        $this->_dropTables = array_diff_key($lastPublishedTables, $currentTables);
        $this->_commonTables = array_intersect_key($currentTables, $lastPublishedTables);

        foreach ($this->_createTables as $tblName => $table) {
            $this->addCreateTable($tblName);
        }
        foreach ($this->_dropTables as $tblName => $table) {
            $this->addDropTable($tblName);
        }

    }

    protected function addCreateTable($tableName)
    {
        $this->down($this->dropTable($tableName));
        $this->up($this->dropTable($tableName));
        $this->up($this->createTable($tableName));
    }

    protected function addDropTable($tableName)
    {
        $this->up($this->dropTable($tableName));
        $this->down($this->dropTable($tableName));
        $this->down($this->createTable($tableName));
    }


    protected function compareCommonTablesScheme()
    {
        if (sizeof($this->_commonTables) > 0)
            foreach ($this->_commonTables as $tblName => $table) {

                $currentTable = $this->_currentDb->getTableColumns($tblName);
                $publishedTable = $this->_publishedDb->getTableColumns($tblName);

                $this->createDifferenceInsideTable($tblName, $currentTable, $publishedTable);


                $this->createIndexDifference($tblName);
            }
    }


    protected function createDifferenceInsideTable($table, $tblCurrentCols, $tblPublishedCols)
    {

        foreach ($tblCurrentCols as $currCol)
        {
            $colForCompare = $this->checkColumnExists($currCol, $tblPublishedCols);

            if (!$colForCompare) {
                $this->up($this->addColumn($table, $currCol));
                $this->down($this->dropColumn($table, $currCol));
            }
            else
            {
                if ($currCol === $colForCompare) continue;
                $sql = $this->changeColumn($table, $currCol);
                $this->up($sql);
                $sql = $this->changeColumn($table, $colForCompare);
                $this->down($sql);
            }
        }


        foreach ($tblPublishedCols as $published_column)
        {

            $has = $this->checkColumnExists($published_column, $tblCurrentCols);

            if (!$has) {
                $constraint = $this->getConstraintForColumn($this->_publishedDb, $table, $published_column['Field']);

                if (count($constraint)) {
                    $this->down($this->addConstraint(array('constraint' => $constraint)));
                    $this->up($this->dropConstraint(array('constraint' => $constraint)));
                }
                $this->down($this->addColumn($table, $published_column));
                $this->up($this->dropColumn($table, $published_column));
            }
        }
    }

    protected function createIndexDifference($table)
    {
        $current_indexes =  $this->_currentDb->getIndexList($table);
        $published_indexes = $this->_publishedDb->getIndexList($table);


        foreach ($current_indexes as $cur_index)
        {
            $index_for_compare = $this->checkIndexExists($cur_index, $published_indexes);
            if (!$index_for_compare) {
                $this->down($this->dropConstraint($cur_index));
                $this->down($this->dropIndex($cur_index));
                $this->up($this->dropConstraint($cur_index));
                $this->up($this->dropIndex($cur_index));
                $this->up($this->addIndex($cur_index));
                $this->up($this->addConstraint($cur_index));
            }
            elseif ($index_for_compare === $cur_index)
            {
                continue;
            }
            else // index exists but not identical
            {
                $this->down($this->dropConstraint($cur_index));
                $this->down($this->dropIndex($cur_index));
                $this->down($this->addIndex($index_for_compare));
                $this->down($this->addConstraint($index_for_compare));
                $this->up($this->dropConstraint($cur_index));
                $this->up($this->dropIndex($cur_index));
                $this->up($this->addIndex($cur_index));
                $this->up($this->addConstraint($cur_index));
            }
        }
    }

    protected function checkColumnExists($column, $colList)
    {

        return (array_key_exists($column['COLUMN_NAME'], $colList)) ?
            $colList[$column['COLUMN_NAME']] : false;

    }

    //-------------------------------------------
    protected function dropTable($t)
    {
        return "DROP TABLE IF EXISTS `{$t}`";
    }

    protected function dropColumn($table, $column)
    {
        return "ALTER TABLE `{$table}` DROP `{$column['COLUMN_NAME']}`";
    }

    protected function addColumn($table, $column)
    {
        $sql = "ALTER TABLE `{$table}` ADD `{$column['COLUMN_NAME']}` " . addslashes($column['DATA_TYPE']);
        $this->addSqlExtras($sql, $column);
        return $sql;
    }

    protected function addSqlExtras(& $sql, $column)
    {
        if (!$column['NULLABLE']) $sql .= " NOT NULL ";
        if (!is_null($column['DEFAULT'])) $sql .= " DEFAULT \\'{$column['DEFAULT']}\\' ";
        if ($column['IDENTITY']) $sql .= ' AUTO_INCREMENT ';

    }

    protected function changeColumn($table, $column)
    {
        $sql = "ALTER TABLE `{$table}` CHANGE " .
            " `{$column['COLUMN_NAME']}` `{$column['COLUMN_NAME']}` " .
            addslashes($column['DATA_TYPE']);
        if ($column['LENGTH']) {
            $sql .= ' (' . $column['LENGTH'] . ')';
        }

        $this->addSqlExtras($sql, $column);
        return $sql;
    }

    protected function createTable($tname)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $trow = $db->fetchRow("SHOW CREATE TABLE `{$tname}`");
        $query = preg_replace('#AUTO_INCREMENT=\S+#is', '', $trow['Create Table']);
        $query = preg_replace("#\n\s*#", ' ', $query);
        $query = addcslashes($query, '\\\''); //escape slashes and single quotes
        return $query;
    }

    protected function checkIndexExists($index, $index_list)
    {
        foreach ($index_list as $comparing_index)
        {
            if ($index['name'] === $comparing_index['name']) {
                return $comparing_index;
            }
        }
        return false;
    }

    protected function addIndex($index)
    {
        if ($index['name'] === 'PRIMARY') {
            $index_string = "ALTER TABLE `{$index['table']}` ADD PRIMARY KEY";
            $fields = array();
            foreach ($index['fields'] as $f)
            {
                $len = intval($f['length']) ? "({$f['length']})" : '';
                $fields[] = "{$f['name']}" . $len;
            }
            $index_string .= "(" . implode(',', $fields) . ")";
        } else {
            $index_string = "CREATE ";
            if ($index['type'] === 'FULLTEXT') $index_string .= " FULLTEXT ";
            if ($index['unique']) $index_string .= " UNIQUE ";
            $index_string .= " INDEX `{$index['name']}` ";
            if (in_array($index['type'], array('RTREE', 'BTREE', 'HASH',))) {
                $index_string .= " USING {$index['type']} ";
            }
            $index_string .= " on `{$index['table']}` ";
            $fields = array();
            foreach ($index['fields'] as $f)
            {
                $len = intval($f['length']) ? "({$f['length']})" : '';
                $fields[] = "{$f['name']}" . $len;
            }
            $index_string .= "(" . implode(',', $fields) . ")";
        }
        return $index_string;
    }

    protected function dropIndex($index)
    {
        return "DROP INDEX `{$index['name']}` ON `{$index['table']}`";
    }

    protected function getConstraintForColumn($table, $colName)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $row = $db->fetchRow("select database() as dbname");

        $dbName = $row['dbname'];

        $sql = "SELECT k.CONSTRAINT_SCHEMA,k.CONSTRAINT_NAME,k.TABLE_NAME,k.COLUMN_NAME,k.REFERENCED_TABLE_NAME,k.REFERENCED_COLUMN_NAME, r.UPDATE_RULE, r.DELETE_RULE FROM information_schema.key_column_usage k LEFT JOIN information_schema.referential_constraints r ON r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND k.REFERENCED_TABLE_NAME=r.REFERENCED_TABLE_NAME LEFT JOIN information_schema.table_constraints t ON t.CONSTRAINT_SCHEMA = r.CONSTRAINT_SCHEMA WHERE k.constraint_schema='$dbName' AND t.CONSTRAINT_TYPE='FOREIGN KEY' AND k.TABLE_NAME='$table' AND r.TABLE_NAME='$table' AND t.TABLE_NAME='$table' AND k.COLUMN_NAME='$colName'";

        $row = $db->fetchRow($sql);

        if (!count($row)) return false;

        $constraint = array(
            'table' => $table,
            'name' => $row['CONSTRAINT_NAME'],
            'column' => $row['COLUMN_NAME'],
            'reference' => array(
                'table' => $row['REFERENCED_TABLE_NAME'],
                'column' => $row['REFERENCED_COLUMN_NAME'],
                'update' => $row['UPDATE_RULE'],
                'delete' => $row['DELETE_RULE'],
            )
        );
        return $constraint;
    }

    protected function dropConstraint($index)
    {
        if (!isset($index['constraint']['column'])
            || !strlen($index['constraint']['column'])
        ) {
            return '';
        }

        $sql = "ALTER TABLE `{$index['constraint']['table']}` " .
            "DROP FOREIGN KEY `{$index['constraint']['name']}` ";

        return $sql;
    }

    protected function addConstraint($index)
    {
        if (!isset($index['constraint']['column']) || !strlen($index['constraint']['column'])) return '';
        $sql = "ALTER TABLE `{$index['constraint']['table']}` " .
            "ADD CONSTRAINT `{$index['constraint']['name']}` " .
            "FOREIGN KEY (`{$index['constraint']['column']}`) " .
            "REFERENCES `{$index['constraint']['reference']['table']}` " .
            "(`{$index['constraint']['reference']['column']}`) " .
            "ON UPDATE {$index['constraint']['reference']['update']} " .
            "ON DELETE {$index['constraint']['reference']['delete']} ";
        return $sql;
    }

}
