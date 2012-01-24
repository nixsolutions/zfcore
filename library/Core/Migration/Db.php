<?php
/**
 * Created by glide/malinovskiy.
 * Date: 20.01.12
 * Time: 16:03
 */
class Core_Migration_Db
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
     * @param null $options
     * @param bool $autoLoad
     */
    public function __construct($options = null,$autoLoad = true)
    {
        if (isset($options['blacklist']) && !isset($options['whitelist'])) {

            if(is_array($options['blacklist'])) {
                $this->_blackList = $options['blacklist'];
            } else {
                $this->_blackList[] = (string)$options['blacklist'];
            }

        } elseif (isset($options['whitelist']) && !empty($options['whitelist'])) {

            if(is_array($options['whitelist'])) {
                $this->_whiteList = $options['whitelist'];
            } else {
                $this->_whiteList[] = (string)$options['whitelist'];
            }

        }


        if($autoLoad) {
            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            $tables  = $dbAdapter->listTables();

            foreach($tables as $table) {

                $scheme = $dbAdapter->describeTable($table);

                $this->addTable($table,$scheme);

                $this->_indexes[$table] = $this->getIndexListFromTable($table);

            }
        }

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

        foreach($indexesData as $index) {
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
        if($this->isTblWhiteListed($tableName) && !$this->isTblBlackListed($tableName))
            $this->_scheme[$tableName] = $scheme;
    }

    /**
     * delete table from DB object
     * @param $tableName
     */
    public function deleteTable($tableName)
    {
        if(array_key_exists($tableName,$this->_scheme))
            unset($this->_scheme[$tableName]);
    }
    /**
     * checks for table in @blacklist
     * @param $tableName
     * @return bool
     */
    protected function isTblWhiteListed($tableName)
    {
        if(!empty($this->_whiteList)) {
            return in_array($tableName,$this->_whiteList);
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
        if(!empty($this->_blackList)) {
            return in_array($tableName,$this->_blackList);
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

            $dec = json_decode($jsonString,true);

            $this->_indexes = $dec['indexes'];
            $dec = $dec['data'];

           foreach($this->_blackList as $deleteKey){
               if(array_key_exists($deleteKey,$dec)) {
                   unset($dec[$deleteKey]);
               }
           }
           foreach($dec as $tblName=>$table){
                if(!in_array($tblName,$this->_whiteList)) {
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
        if(array_key_exists($tableName,$this->_indexes))
            return $this->_indexes[$tableName];
        else
            return array();
    }


}
