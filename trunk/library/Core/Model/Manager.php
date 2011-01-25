<?php
/**
 * Class Core_Model_Mapper
 *
 * mapper for model
 *
 * @package  Core_Model_Mapper
 *
 * @author   dark
 * @created  Tue Apr 20 08:17:02 GMT 2010
 * @version  $Id$
 */
class Core_Model_Manager
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    
    /**
     * @var string
     */
    protected $_modelName;
    
    /**
     * set dbTable instance
     *
     * @param Zend_Db_Table_Abstract|string $dbTable
     * @return Core_Model_Mapper
     */
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    /**
     * return dbTable instance
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $dbTable = $this->getModelName() .'_Table';
            $this->setDbTable($dbTable);
        }
        return $this->_dbTable;
    }
    
    /**
     * set name of model
     *
     * @param  string  $model
     * @return Core_Model_Mapper
     */
    public function setModelName($model)
    {
        $this->_modelName = $model;
        return $this;
    }
    
    /**
     * return current model name
     * 
     * @return  string
     */
    public function getModelName() 
    {
        if (null === $this->_modelName) {
            $modelName = get_class($this);
            $modelName = substr($modelName, 0, strpos($modelName, '_Manager'));
            $this->setModelName($modelName);
        }
        return $this->_modelName;
    }
}