<?php
/**
 * Created by glide/malinovskiy.
 * Date: 24.01.12
 * Time: 15:46
 */
class Core_Db_Database
{
    /**
     * array with schemes of tables
     * @var array
     */
    protected $_scheme = array();

    /**
     * array with indexes of tables
     * @var array
     */
    protected $_indexes = array();

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * array with ignored tables
     * @var array
     */
    protected $_blackList = array();

    /**
     * array with "white listed" tables
     * @var array
     */
    protected $_whiteList = array();

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * @var array
     */
    protected $_options = array();


    /**
     * @param null $options
     * @param bool $autoLoad
     */
    public function __construct($options = null,$autoLoad = true)
    {

        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_options = $options;


        if (isset($options['blacklist']) && !isset($options['whitelist'])) {

            if (is_array($options['blacklist'])) {
                $this->_blackList = $options['blacklist'];
            } else {
                $this->_blackList[] = (string)$options['blacklist'];
            }

        } elseif (isset($options['whitelist']) && !empty($options['whitelist'])) {

            if (is_array($options['whitelist'])) {
                $this->_whiteList = $options['whitelist'];
            } else {
                $this->_whiteList[] = (string)$options['whitelist'];
            }

        }


        if ($autoLoad) {

            $tables  = $this->_db->listTables();

            foreach ($tables as $table) {

                $scheme = $this->_db->describeTable($table);

                $this->addTable($table, $scheme);
            }
        }

    }

    public function getDump()
    {
        $dump = '';

        foreach ($this->_scheme as $tableName=>$fields) {

            $dump .= self::dropTable($tableName).';'.PHP_EOL;
            $dump .= self::createTable($tableName).';'.PHP_EOL;

            if (sizeof($this->_data[$tableName]) > 0)
                foreach ($this->_data[$tableName] as $data)
                    $dump .= self::insert($tableName, $data).';'.PHP_EOL;
        }

       return stripslashes($dump);
    }



    /**
     * retrieve index list from table
     * @param $table - table name
     * @return array - array of indexes
     */

    protected function getIndexListFromTable($table)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $sql = "SHOW INDEXES FROM `{$table}`";
        $indexesData = $db->fetchAll($sql);
        $indexes = array();

        foreach ($indexesData as $index) {
            if (!isset($indexes[$index['Key_name']])) $indexes[$index['Key_name']] = array();
            $indexes[$index['Key_name']]['unique'] = !intval($index['Non_unique']);
            $indexes[$index['Key_name']]['type'] = $index['Index_type'];
            $indexes[$index['Key_name']]['name'] = $index['Key_name'];
            $indexes[$index['Key_name']]['table'] = $index['Table'];
            if (!isset($indexes[$index['Key_name']]['fields'])) $indexes[$index['Key_name']]['fields'] = array();
            $indexes[$index['Key_name']]['fields'][$index['Seq_in_index']] =
                array(
                    'name' => $index['Column_name'],
                    'length' => $index['Sub_part']
                );
            $indexes[$index['Key_name']]['constraint'] = $this->getConstraintForColumn($table, $index['Column_name']);

        }
        return $indexes;

    }

    /**
     * @param $table - table name
     * @param $colName - column name
     * @return array|bool - return list of constrains or false, if constrains not exist
     */

    protected function getConstraintForColumn($table, $colName)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $row = $db->fetchRow("select database() as dbname");

        $dbName = $row['dbname'];

        $sql = "SELECT k.CONSTRAINT_SCHEMA,k.CONSTRAINT_NAME,"
            ."k.TABLE_NAME,k.COLUMN_NAME,k.REFERENCED_TABLE_NAME,"
            ."k.REFERENCED_COLUMN_NAME, r.UPDATE_RULE, r.DELETE_RULE FROM "
            ."information_schema.key_column_usage k LEFT JOIN "
            ."information_schema.referential_constraints r ON "
            ."r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND "
            ." k.REFERENCED_TABLE_NAME=r.REFERENCED_TABLE_NAME "
            ."LEFT JOIN information_schema.table_constraints t ON "
            ."t.CONSTRAINT_SCHEMA = r.CONSTRAINT_SCHEMA WHERE "
            ." k.constraint_schema='$dbName' AND t.CONSTRAINT_TYPE='FOREIGN KEY' "
            ."AND k.TABLE_NAME='$table' AND r.TABLE_NAME='$table' "
            ." AND t.TABLE_NAME='$table' AND k.COLUMN_NAME='$colName'";

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

    /**
     * add table to DB object
     * @param $tableName
     * @param $scheme - table structure from DESCRIBE TABLE query
     */

    public function addTable($tableName, $scheme)
    {
        if ($this->isTblWhiteListed($tableName) && !$this->isTblBlackListed($tableName)) {
            $this->_scheme[$tableName] = $scheme;

            $this->_indexes[$tableName] = $this->getIndexListFromTable($tableName);

            if (isset($this->_options['loaddata']) && $this->_options['loaddata'] == true) {

                $this->_data[$tableName] = $this->_db->fetchAll(
                    $this->_db->select()->from($tableName)
                );

            }
        }
    }

    /**
     * delete table from DB object
     * @param $tableName
     */
    public function deleteTable($tableName)
    {
        if (array_key_exists($tableName, $this->_scheme))
            unset($this->_scheme[$tableName]);
    }
    /**
     * checks for table in @blacklist
     * @param $tableName
     * @return bool
     */
    protected function isTblWhiteListed($tableName)
    {
        if (!empty($this->_whiteList)) {
            return in_array($tableName, $this->_whiteList);
        }
        return true;
    }

    /**
     * checks for table in @whitelist
     * @param $tableName
     * @return bool
     */
    protected function isTblBlackListed($tableName)
    {
        if (!empty($this->_blackList)) {
            return in_array($tableName, $this->_blackList);
        }
        return false;

    }

    /**
     * encode object data into JSON
     * @return string - JSON string
     */
    public function toString()
    {
        return json_encode(
            array('data'=>$this->_scheme,'indexes'=>$this->_indexes)
        );
    }

    /**
     * decode object from JSON string
     * clear scheme and indexes if string is empty
     * @param $jsonString
     */
    public function fromString($jsonString)
    {
        if (!empty($jsonString)) {

            $dec = json_decode($jsonString, true);

            $this->_indexes = $dec['indexes'];
            $dec = $dec['data'];

            foreach ($this->_blackList as $deleteKey) {
                if (array_key_exists($deleteKey, $dec)) {
                    unset($dec[$deleteKey]);
                }
            }
            foreach ($dec as $tblName=>$table) {
                if (!in_array($tblName, $this->_whiteList)) {
                    unset($dec[$tblName]);
                }
            }

            $this->_scheme = $dec;
        } else {
            $this->_scheme = array();
            $this->_indexes = array();
        }

    }

    /**
     * get all tables form DB
     * @return array
     */
    public function getTables()
    {
        return $this->_scheme;
    }

    /**
     * get all columns from table
     * @param $tableName
     * @return array|bool - returns false if table not exist
     */
    public function getTableColumns($tableName)
    {
        return (isset($this->_scheme[$tableName])) ?
            $this->_scheme[$tableName] : false;

    }

    /**
     * get all table indexes
     * @param $tableName
     * @return array - returns empty array if no indexes found
     */
    public function getIndexList($tableName)
    {
        if (array_key_exists($tableName, $this->_indexes))
            return $this->_indexes[$tableName];
        else
            return array();
    }



    /**
     * create DROP TABLE query
     * @param $tableName
     * @return string
     */
    public static function dropTable($tableName)
    {
        return "DROP TABLE IF EXISTS `{$tableName}`";
    }

    /**
     * create query for delete column
     * @param $tableName
     * @param $column
     * @return string
     */
    public static function dropColumn($tableName, $column)
    {
        return "ALTER TABLE `{$tableName}` DROP `{$column['COLUMN_NAME']}`";
    }

    /**
     * add column attributes to query
     * @param $sql
     * @param $column
     */
    protected static function addSqlExtras(& $sql, $column)
    {
        if ($column['LENGTH']) $sql .= ' (' . $column['LENGTH'] . ')';
        if ($column['UNSIGNED']) $sql .= ' UNSIGNED ';

        if (!$column['NULLABLE']) $sql .= " NOT NULL ";
        if (!is_null($column['DEFAULT'])) $sql .= " DEFAULT \\'{$column['DEFAULT']}\\' ";
        if ($column['IDENTITY']) $sql .= ' AUTO_INCREMENT ';

    }

    /**
     * create query for adding column
     * @param $tableName
     * @param $column
     * @return string
     */
    public static function addColumn($tableName, $column)
    {
        $sql = "ALTER TABLE `{$tableName}` ADD `{$column['COLUMN_NAME']}` " . addslashes($column['DATA_TYPE']);
        Core_Db_Database::addSqlExtras($sql, $column);
        return $sql;
    }

    /**
     * create query for change column
     * @param $tableName
     * @param $column
     * @return string
     */
    public static function changeColumn($tableName, $column)
    {
        $sql = "ALTER TABLE `{$tableName}` CHANGE " .
            " `{$column['COLUMN_NAME']}` `{$column['COLUMN_NAME']}` " .
            addslashes($column['DATA_TYPE']);
        Core_Db_Database::addSqlExtras($sql, $column);
        return $sql;
    }
    /**
     * create CREATE TABLE query
     * @param $tblName
     * @return string
     */
    public static function createTable($tblName)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $trow = $db->fetchRow("SHOW CREATE TABLE `{$tblName}`");
        $query = preg_replace('#AUTO_INCREMENT=\S+#is', '', $trow['Create Table']);
        //$query = preg_replace("#\n\s*#", ' ', $query); //uncomment if you want query in one line
        $query = addcslashes($query, '\\\''); //escape slashes and single quotes
        return $query;
    }

    /**
     * create query for adding index
     * @param $index
     * @return string
     */
    public static function addIndex($index)
    {
        if ($index['name'] === 'PRIMARY') {
            $indexString = "ALTER TABLE `{$index['table']}` ADD PRIMARY KEY";
            $fields = array();
            foreach ($index['fields'] as $f) {
                $len = intval($f['length']) ? "({$f['length']})" : '';
                $fields[] = "{$f['name']}" . $len;
            }
            $indexString .= "(" . implode(',', $fields) . ")";
        } else {
            $indexString = "CREATE ";
            if ($index['type'] === 'FULLTEXT') $indexString .= " FULLTEXT ";
            if ($index['unique']) $indexString .= " UNIQUE ";
            $indexString .= " INDEX `{$index['name']}` ";
            if (in_array($index['type'], array('RTREE', 'BTREE', 'HASH',))) {
                $indexString .= " USING {$index['type']} ";
            }
            $indexString .= " on `{$index['table']}` ";
            $fields = array();
            foreach ($index['fields'] as $f) {
                $len = intval($f['length']) ? "({$f['length']})" : '';
                $fields[] = "{$f['name']}" . $len;
            }
            $indexString .= "(" . implode(',', $fields) . ")";
        }
        return $indexString;
    }

    /**
     * create query for drop index
     * @param $index
     * @return string
     */
    public static function dropIndex($index)
    {
        return "DROP INDEX `{$index['name']}` ON `{$index['table']}`";
    }

    /**
     * create query for drop constraint
     * @param $index
     * @return string
     */
    public static function dropConstraint($index)
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

    /**
     * create query for adding constraint
     * @param $index
     * @return string
     */
    public static function addConstraint($index)
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


    public static function insert($table, array $bind)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        // extract and quote col names from the array keys
        $cols = array();
        $vals = array();
        $i = 0;
        foreach ($bind as $col => $val) {
            $cols[] = '`'.$col.'`';
            $vals[] = $db->quote($val);

        }

        // build the statement
        $sql = "INSERT INTO `"
            . $table
            . '` (' . implode(', ', $cols) . ') '
            . 'VALUES (' . implode(', ', $vals) . ')';

        return $sql;
    }

}

