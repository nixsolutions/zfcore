<?php
/**
 * Created by glide/malinovskiy.
 * Date: 20.01.12
 * Time: 16:03
 */
class Core_Migration_Db
{

    protected $_data = array();
    protected $_indexes = array();
    protected $_blackList = array();

    public function __construct($options = null,$autoLoad = true)
    {
        if (isset($options['blacklist'])) {

            if(is_array($options['blacklist'])) {
                $this->_blackList = $options['blacklist'];
            } else {
                $this->_blackList[] = (string)$options['blacklist'];
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

    public function addTable($tableName, $scheme)
    {
        if(!in_array($tableName,$this->_blackList))
            $this->_data[$tableName] = $scheme;
    }

    public function deleteTable($tableName)
    {
        unset($this->_data[$tableName]);
    }

    public function toString()
    {
        return json_encode(
            array('data'=>$this->_data,'indexes'=>$this->_indexes)
        );
    }

    public function fromString($jsonString)
    {
        $dec = (array)json_decode($jsonString,true);

        $this->_indexes = $dec['indexes'];
        $dec = $dec['data'];

       foreach($this->_blackList as $deleteKey){
           if(array_key_exists($deleteKey,$dec)) {
               unset($dec[$deleteKey]);
           }
       }
        $this->_data = $dec;
    }

    public function getTables()
    {
        return $this->_data;
    }

    public function getTableColumns($tableName)
    {
        return (isset($this->_data[$tableName])) ?
            $this->_data[$tableName] : false;

    }
    public function getIndexList($table)
    {
        if(array_key_exists($table,$this->_indexes))
            return $this->_indexes[$table];
        else
            return array();
    }


}
