<?php
/**
 * Created by glide/malinovskiy.
 * Based on dbDiff.class.php (max.antonoff@gmail.com)
 * Date: 20.01.12
 * Time: 17:09
 */

class Core_Db_Database_Diff
{
    /**
     * @var Core_Db_Database
     */
    protected $_currentDb;
    /**
     * @var Core_Db_Database
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
    /**
     * add query to upgrade actions
     * @param $query
     */
    protected function up($query)
    {
        if (!empty($query)) {
            $this->_difference['up'][] = $query;
        }
    }

    /**
     * add query to downgrade action
     * @param $query
     */
    protected function down($query)
    {
        if (!empty($query)) {
            $this->_difference['down'][] = $query;
        }
    }

    /**
     * get difference between databases
     * @return array - with two subarrays: "up" & "down"
     */
    public function getDifference()
    {
        $this->compareTables();

        $this->compareCommonTablesScheme();

        return $this->_difference;
    }

    /**
     * get difference between tables in databases
     */
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

    /**
     * add table creation action to "upgrade"
     * @param $tableName
     */
    protected function addCreateTable($tableName)
    {
        $this->down(Core_Db_Database::dropTable($tableName));
        
        //Core_Db_Database::dropTable()
        
        $this->up(Core_Db_Database::dropTable($tableName));
        $this->up(Core_Db_Database::createTable($tableName));
    }
    /**
     * add drop creation action to "upgrade"
     * @param $tableName
     */
    protected function addDropTable($tableName)
    {
        $this->up(Core_Db_Database::dropTable($tableName));
        $this->down(Core_Db_Database::dropTable($tableName));
        $this->down(Core_Db_Database::createTable($tableName));
    }

    /**
     * compare schemes of common tables
     */
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

    /**
     * get difference between two schemes of tables
     * @param $table
     * @param $tblCurrentCols
     * @param $tblPublishedCols
     */
    protected function createDifferenceInsideTable($table, $tblCurrentCols, $tblPublishedCols)
    {

        foreach ($tblCurrentCols as $currCol) {
            $colForCompare = $this->checkColumnExists($currCol, $tblPublishedCols);

            if (!$colForCompare) {
                $this->up(Core_Db_Database::addColumn($table, $currCol));
                $this->down(Core_Db_Database::dropColumn($table, $currCol));
            } else {
                if ($currCol === $colForCompare) continue;
                $sql = Core_Db_Database::changeColumn($table, $currCol);
                $this->up($sql);
                $sql = Core_Db_Database::changeColumn($table, $colForCompare);
                $this->down($sql);
            }
        }


        foreach ($tblPublishedCols as $publishedColumn) {

            $has = $this->checkColumnExists($publishedColumn, $tblCurrentCols);

            if (!$has) {
                $constraint = $this->getConstraintForColumn($table, $publishedColumn['COLUMN_NAME']);

                if (count($constraint)) {
                    $this->down(Core_Db_Database::addConstraint(array('constraint' => $constraint)));
                    $this->up(Core_Db_Database::dropConstraint(array('constraint' => $constraint)));
                }
                $this->down(Core_Db_Database::addColumn($table, $publishedColumn));
                $this->up(Core_Db_Database::dropColumn($table, $publishedColumn));
            }
        }
    }


    /**
     * Get difference between table indexes
     *
     * @param $table
     */
    protected function createIndexDifference($table)
    {
        $currentIndexes =  $this->_currentDb->getIndexList($table);
        $publishedIndexes = $this->_publishedDb->getIndexList($table);

        foreach ($currentIndexes as $curIndex) {
            $indexForCompare = $this->findIndex($curIndex, $publishedIndexes);
            if (!$indexForCompare) {
                $this->up(Core_Db_Database::addIndex($curIndex));
                $this->up(Core_Db_Database::addConstraint($curIndex));

                $this->down(Core_Db_Database::dropConstraint($curIndex));
                $this->down(Core_Db_Database::dropIndex($curIndex));
            } elseif ($indexForCompare !== $curIndex) {
                $this->up(Core_Db_Database::dropConstraint($curIndex));
                $this->up(Core_Db_Database::dropIndex($curIndex));

                $this->down(Core_Db_Database::dropConstraint($curIndex));
                $this->down(Core_Db_Database::dropIndex($curIndex));
                $this->down(Core_Db_Database::addIndex($indexForCompare));
                $this->down(Core_Db_Database::addConstraint($indexForCompare));
            }
        }

        //For creating deleted indexes
        $deletedIndexes = $this->getDeletedIndexes($currentIndexes, $publishedIndexes);
        if ($deletedIndexes) {
            foreach ($deletedIndexes as $deletedIndex) {
                //Create deleted index
                $this->up(Core_Db_Database::dropConstraint($deletedIndex));
                $this->up(Core_Db_Database::dropIndex($deletedIndex));
                //Delete index
                $this->down(Core_Db_Database::addConstraint($deletedIndex));
                $this->down(Core_Db_Database::addIndex($deletedIndex));
            }
        }
    }


    /**
     * @param $column
     * @param $colList
     * @return bool
     */
    protected function checkColumnExists($column, $colList)
    {

        return (array_key_exists($column['COLUMN_NAME'], $colList)) ?
            $colList[$column['COLUMN_NAME']] : false;

    }

    /**
     * Find Index exists
     * @param $index
     * @param $indexList
     * @return null | array - if index exist return index
     */
    protected function findIndex($index, $indexList)
    {
        foreach ($indexList as $comparingIndex) {
            if ($index['name'] === $comparingIndex['name']) {
                return $comparingIndex;
            }
        }
        return null;
    }


    /**
     * Get deleted indexes in current DB
     *
     * @param array $currentIndexes
     * @param array $publishedIndexes
     * @return array
     */
    protected function getDeletedIndexes($currentIndexes, $publishedIndexes)
    {
        $nonExistIndexes = array();
        foreach ($publishedIndexes as $publishedIndex) {
            $exist = false;
            foreach ($currentIndexes as $currentIndex) {

                if ($currentIndex['name'] === $publishedIndex['name']) {
                    $exist = true;
                    break;
                }
            }

            if (!$exist) {
                $nonExistIndexes[] = $publishedIndex;
            }
        }
        return $nonExistIndexes;
    }


    protected function getConstraintForColumn($table, $colName)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $row = $db->fetchRow("select database() as dbname");

        $dbName = $row['dbname'];

        $sql = "SELECT k.CONSTRAINT_SCHEMA,
                       k.CONSTRAINT_NAME,
                       k.TABLE_NAME,
                       k.COLUMN_NAME,
                       k.REFERENCED_TABLE_NAME,
                       k.REFERENCED_COLUMN_NAME,
                       r.UPDATE_RULE,
                       r.DELETE_RULE
                       FROM information_schema.key_column_usage k
                       LEFT JOIN information_schema.referential_constraints r
                       ON r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA
                       AND k.REFERENCED_TABLE_NAME=r.REFERENCED_TABLE_NAME
                       LEFT JOIN information_schema.table_constraints t
                       ON t.CONSTRAINT_SCHEMA = r.CONSTRAINT_SCHEMA
                       WHERE
                        k.constraint_schema='$dbName'
                        AND t.CONSTRAINT_TYPE='FOREIGN KEY'
                        AND k.TABLE_NAME='$table'
                        AND r.TABLE_NAME='$table'
                        AND t.TABLE_NAME='$table'
                        AND k.COLUMN_NAME='$colName'";

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

}
