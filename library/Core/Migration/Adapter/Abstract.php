<?php

/**
 * Class Core_Migration_Abstract
 *
 * abstract migration
 *
 * @category Core
 * @package  Core_Migration_Adapter
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * 
 * @version  $Id: Abstract.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
abstract class Core_Migration_Adapter_Abstract
{
    /**
     * Default Database adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_dbAdapter = null;
    
    
    public function __construct(Zend_Db_Adapter_Abstract $dbAdapter)
    {
        $this->_dbAdapter = $dbAdapter;
    } 
    
     /**
     * setDbAdapter
     *
     * @param  Zend_Db_Adapter_Abstract $dbAdapter
     * @return return
     */
    protected function setDbAdapter($dbAdapter = null)
    {
        if ($dbAdapter && ($dbAdapter instanceof Zend_Db_Adapter_Abstract)) {
            $this->_dbAdapter = $dbAdapter;
        } else {
            $this->_dbAdapter = Zend_Db_Table::getDefaultAdapter();
        }
        return $this;
    }
    /**
     * getDbAdapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter()
    {
        if (!$this->_dbAdapter) {
            $this->setDbAdapter();
        }
        return $this->_dbAdapter;
    } 
    
    /**
     * query
     *
     * @param   string     $query
     * @return  Core_Migration_Abstract
     */
    public function query($query) 
    {
        $this->getDbAdapter()->query($query);
        return $this;
    }
    
    
    /**
     * createTable
     *
     * @param   string $table table name
     * @return  Core_Migration_Abstract
     */
    abstract public function createTable($table); 
    
    /**
     * dropTable
     *
     * @param   string     $table  table name
     * @return  Core_Migration_Abstract
     */
    abstract public function dropTable($table); 
   
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
    abstract public function createColumn($table, 
                                 $column,
                                 $datatype,
                                 $length = null,
                                 $default = null,
                                 $notnull = false,
                                 $primary = false
                                 ); 
   
    
    /**
     * dropColumn
     *
     * @param   string   $table
     * @param   string   $name 
     * @return  bool
     */
    abstract public function dropColumn($table, $name);
    
    /**
     * Create an unique index on table
     *
     * @param string $table
     * @param array $columns
     * @param string $indName
     * @return Core_Migration_Adapter_Abstract
     */
    abstract public function createUniqueIndexes($table, array $columns, $indName = null);
    
     /**
     * Drop an index on table
     *
     * @param string $indName
     * @return Core_Migration_Adapter_Abstract
     */
    abstract public function dropUniqueIndexes($table, $indName);
    
    /**
     * __call for unsupported adaptors methods
     *
     * @param string $name Method name
     * @param mixed $arguments Method Arguments
     * return Core_Migration_Adapter_Abstract
     */
    public function  __call($name, $arguments)
    {
        return $this;
    }
}