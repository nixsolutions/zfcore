<?php

/**
 * Class Core_Migration_Adapter_Mysql
 *
 * 
 *
 * @category Core
 * @package  Core_Migration_Adapter
 *
 * @author   Alexey Novikov <oleksii.novikov@gmail.com>
 * 
 * @version  
 */
class Core_Migration_Adapter_Mysql extends Core_Migration_Adapter_Abstract
{

    /**
     * Insert
     *
     * @param string $table
     * @param array $params
     * @return Core_Migration_Adapter_Abstract
     */
    public function insert($table, array $params)
    {
        return $this->getDbAdapter()->insert($table, $params);
    }
    
    /**
     * Updates table rows with specified data based on a WHERE clause.
     *
     * @param  mixed        $table The table to update.
     * @param  array        $bind  Column-value pairs.
     * @param  mixed        $where UPDATE WHERE clause(s).
     * @return int          The number of affected rows.
     */
    public function update($table, array $bind, $where = '')
    {
        return $this->getDbAdapter()->update($table, $bind, $where);
    }
    
    /**
     * Create table
     *
     * @param string $table
     * @return Core_Migration_Adapter_Abstract
     */    
    public function createTable($table) 
    {
        $this->query(
            'CREATE TABLE '.
            $table.
            ' ( `id` bigint NOT NULL AUTO_INCREMENT , PRIMARY KEY (`id`))'.
            ' Engine=InnoDB'
        );
        return $this;
    }
    
    /**
     * dropTable
     *
     * @param   string     $table  table name
     * @return  Core_Migration_Abstract
     */
    public function dropTable($table) 
    {
        $this->query('DROP TABLE '.$table);
        return $this;
    }
    
    /**
     * createColumn
     *
     * FIXME: requried quoted queries data
     * 
     * @param   string   $table
     * @param   string   $column 
     * @param   string   $datatype
     * @param   string   $length
     * @param   string   $default
     * @param   bool     $notnull
     * @param   bool     $primary
     * @return  bool
     */
    public function createColumn($table, 
                                 $column,
                                 $datatype,
                                 $length = null,
                                 $default = null,
                                 $notnull = false,
                                 $primary = false
                                 ) 
    {
        
        // alter table $table add column $column $options
        // alter table `p_zfc`.`asd` add column `name` varchar(123) NOT NULL after `id`
        $column = $this->getDbAdapter()->quoteIdentifier($column);
        $query = 'ALTER TABLE ' . $this->getDbAdapter()->quoteIdentifier($table)
               . ' ADD COLUMN '.$column;
        
        // switch statement for $datatype
        switch ($datatype) {
            case Core_Migration_Abstract::TYPE_VARCHAR:
                $length = $length?$length:255;
                $query .= " varchar($length)";
                break;
            case Core_Migration_Abstract::TYPE_FLOAT:
                $length = $length?$length:'0,0';
                $query .= " float($length)";
                break;
            case Core_Migration_Abstract::TYPE_ENUM:
                if (is_array($length)) {
                    // array to string 'el','el',...
                    $length = "'" . join("','", $length) . "'";                    
                }
                $query .= " enum($length)";
                break;
            default:
                $query .= " $datatype";
                break;
        }
        
        if (!is_null($default)) {
            // switch statement for $datatype
            switch ($datatype) {
                case (Core_Migration_Abstract::TYPE_TIMESTAMP && $default == 'CURRENT_TIMESTAMP'):
                    $query .= " default CURRENT_TIMESTAMP";
                    break;
                default:
                    $query .= ' default ' . $this->getDbAdapter()->quote($default);
                    break;
            }
        }
        
        if ($notnull) {
            $query .= " NOT NULL";
        } else {
            $query .= " NULL";
        }
        
        if ($primary) {
            // TODO: drop primary key, add primary key (`all keys`,`$column`)
            $fields = $this->getDbAdapter()->describeTable($table);
            $primary = array();
            foreach ($fields as $field => $options) {
                if ($options['PRIMARY'])
                    array_push($primary, $field);
            }
            
            if (sizeof($primary)) {
                $keys = $quotedColumns = $this->_quoteIdentifierArray($primary);
                $query .= ", drop primary key, add primary key ($keys, $column)";
            } else {
                $query .= ", add primary key ($column)";
            }
        }

        $this->query($query);
        
        return $this;
    }
    
    /**
     * dropColumn
     *
     * @param   string   $table
     * @param   string   $name 
     * @return  bool
     */
    public function dropColumn($table, $name) 
    {
        $this->query(
            'ALTER TABLE ' . $this->getDbAdapter()->quoteIdentifier($table) .
            ' DROP COLUMN ' . $this->getDbAdapter()->quoteIdentifier($name)
        );
        return $this;
    }   
        
    /**
     * Create an unique index on table
     *
     * @param string $table
     * @param array $columns
     * @param string $indName
     * @return Core_Migration_Adapter_Abstract
     */
    public function createUniqueIndexes($table, array $columns, $indName = null)
    {
        if (!$indName) {
            $indName = strtoupper($table . '_' . implode('_', $columns));
        }   
        //quoting a columns 
        $quotedColumns = $this->_quoteIdentifierArray($columns);
        $query = 'ALTER TABLE ' . $this->getDbAdapter()->quoteIdentifier($table) 
            . ' ADD UNIQUE ' . $this->getDbAdapter()->quoteIdentifier($indName)
            . '(' . $quotedColumns . ')';
        $this->query($query);   
           
        return $this;
    }
    
    /**
     * Drop an index on table
     *
     * @param string $indName
     * @return Core_Migration_Adapter_Abstract
     */
    public function dropUniqueIndexes($table, $indName)
    {
        if ($table && $indName) {
            $query = 'DROP INDEX ' . $this->getDbAdapter()->quoteIdentifier($indName)
                . ' ON ' . $this->getDbAdapter()->quoteIdentifier($table);
            $this->query($query);
        } else {
            throw new Core_Exception("
                Can't drop index " . $indName . " ON " . $table
            );
        }
        return $this;
    }
    
    /**
     * Quoting array of identifier and converts it to coma separated string
     *
     * @param array $columns
     * @return string
     */
    protected function _quoteIdentifierArray(array $columns)
    {
        $quotedColumns = array();    
        foreach ($columns as $value) {
            $quotedColumns[] = $this->getDbAdapter()->quoteIdentifier($value);
        }    
            
        return implode(',', $quotedColumns);
    }
}