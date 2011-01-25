<?php
/**
 * Class Model_Option
 *
 * for options table
 *
 * @category Application
 * @package  Model
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * 
 * @version  $Id: Option.php 207 2010-10-20 15:37:18Z AntonShevchuk $
 */
class Model_Option extends Core_Model_Manager
{
    const TYPE_INT    = 'int';
    const TYPE_FLOAT  = 'float';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY  = 'array';
    const TYPE_OBJECT = 'object';
    
    static protected $_instance = null;
    
    protected $_modelName = 'Model_Option';
    protected $_cache   = array();
    
    
    /**
     * getInstance
     *
     * @return  Model_Option
     */
    static public function getInstance() 
    {
        if (self::$_instance === null) {
            self::$_instance = new Model_Option();
        }
        return self::$_instance;
    }
    
    /**
     * Clear cache
     *
     * @return Core_Model_Table
     */
    static public function clearCache($key = null, $namespace = 'default')
    {
        $self = Model_Option::getInstance();
        
        if (null !== $key && isset($self->_cache[$namespace][$key])) {
            unset($self->_cache[$namespace][$key]);
        } elseif (isset($self->_cache[$namespace])) {
            unset($self->_cache[$namespace]);
        }
        return self::$_instance;
    }
    
    /**
     * get
     *
     * return options from DBTable
     *
     * @param   string $key  
     * @param   string $namespace
     * @return  mixed
     */
    static public function get($key, $namespace = 'default') 
    {
        $self = Model_Option::getInstance();
        if (!isset($self->_cache[$namespace][$key])) {
            $select = $self->getDbTable()->select();
            $select->where('name = ?', $key)
                   ->where('namespace = ?', $namespace);
            $row = $self->getDbTable()->fetchRow($select);
            
            if ($row) {
                $self->_cache[$namespace][$key] = $self->_wakeup($row);
            } else {
                $self->_cache[$namespace][$key] = null;
            }
        }
        return $self->_cache[$namespace][$key];
    }
    
    /**
     * set
     *
     * set options to DBTable
     *
     * @param   string $key  
     * @param   mixed  $value  
     * @param   string $type
     * @return  Model_Option
     */
    static public function set($key, $value, $namespace = 'default', $type = null) 
    {
        $self = Model_Option::getInstance();
        $select = $self->getDbTable()->select();
        $select->where('name = ?', $key)
               ->where('namespace = ?', $namespace);
        $row = $self->getDbTable()->fetchRow($select);
        
        if (!$row) {
            $row = $self->getDbTable()->createRow();
        }
        
        if (!$type) {
            $type = $self->_type($value);
        }
        
        $row->name  = $key;
        $row->value = $self->_sleep($value, $type);
        $row->type  = $type;
        $row->namespace = $namespace;
        $row->save();
             
        $self->_cache[$namespace][$key] = $value;
        return $self;
    }
    
    /**
     * delete
     *
     * delete option from DBTable
     *
     * @param   string $key  
     * @return  Model_Option
     */
    static public function delete($key, $namespace = 'default') 
    {
        $self = Model_Option::getInstance();
        
//        $where = $self->getDbTable()
//                      ->getAdapter()
//                      ->quoteInto('name = ? AND namespace = ?', 
//                                  array($key, $namespace));
//        
//        $self->getDbTable()->delete($where);
        
        
        $select = $self->getDbTable()->select();
        $select->where('name = ?', $key)
               ->where('namespace = ?', $namespace);
        $row = $self->getDbTable()->fetchRow($select);
        
        if ($row) {
            $row->delete();
        }
        
        if (isset($self->_cache[$namespace][$key])) {
            unset($self->_cache[$namespace][$key]);
        }
        
        return $self;
    }
    
    /**
     * getNamespace
     *
     * delete all data from namespace
     *
     * @param   string     $namespace
     * @return  array
     */
    static public function getNamespace($namespace) 
    {
        $self = Model_Option::getInstance();
        
        if (!isset($self->_cache[$namespace])) {
            $select = $self->getDbTable()->select();
            $select->where('namespace = ?', $namespace);
            $rows = $self->getDbTable()->fetchAll($select);
            
            if (sizeof($rows) > 0) {
                foreach ($rows as $row) {
                    $self->_cache[$namespace][$row->name] = $self->_wakeup($row);
                }
            } else {
                $self->_cache[$namespace] = array();
            }
        }
        
        return $self->_cache[$namespace];
    }
    
    /**
     * setNamespace
     *
     * @param   string $namespace
     * @param   array  $options  pairs key=>value
     * @return  Model_Option
     */
    static public function setNamespace($namespace, array $options) 
    {
        // foreach loop for $options array
        foreach ($options as $key => $value) {
            Model_Option::set($key, $value, $namespace);
        }
        return Model_Option::getInstance();
    }
    
    /**
     * deleteNamespace
     *
     * delete all data from namespace
     *
     * @param   string     $namespace
     * @return  Model_Option
     */
    static public function deleteNamespace($namespace) 
    {
        $self = Model_Option::getInstance();
        
        $where = $self->getDbTable()->getAdapter()
                                    ->quoteInto('namespace = ?', $namespace);
        $self->getDbTable()->delete($where);
        
        if (isset($self->_cache[$namespace])) {
            unset($self->_cache[$namespace]);
        }
        
        return $self;
    }
    
    /**
     * _wakeup
     *
     * @param   Zend_Db_Table_Row_Abstract $row
     * @return  mixed
     */
    protected function _wakeup($row) 
    {
        // switch statement for $row->type
        switch ($row->type) {
            case Model_Option::TYPE_INT:
                return (int)$row->value;
                break;
            case Model_Option::TYPE_FLOAT:
                return floatval($row->value);
                break;
            case Model_Option::TYPE_STRING:
                return $row->value;
                break;
            case Model_Option::TYPE_ARRAY:
            case Model_Option::TYPE_OBJECT:
                return unserialize($row->value);
                break;
            default:
                return null;
                break;
        }
    }
    
    /**
     * _sleep
     *
     * @param   mixed $value
     * @return  mixed
     */
    protected function _sleep($value, $type) 
    {
        // switch statement for $row->type
        switch ($type) {
            case Model_Option::TYPE_INT:
            case Model_Option::TYPE_FLOAT:
                return "$value";
                break;
            case Model_Option::TYPE_STRING:
                return "$value";
                break;
            case Model_Option::TYPE_ARRAY:
            case Model_Option::TYPE_OBJECT:
                return serialize($value);
                break;
            default:
                return null;
                break;
        }
    }
    
    /**
     * _type
     *
     * @param   mixed $value
     * @return  mixed 
     */
    protected function _type($value) 
    {
        // switch statement for true
        switch (true) {
            case is_bool($value):  
            case is_integer($value):
                return Model_Option::TYPE_INT;
                break;
            case is_float($value):
                return Model_Option::TYPE_FLOAT;
                break;
            case is_string($value):
                return Model_Option::TYPE_STRING;
                break;
            case is_array($value):
                return Model_Option::TYPE_ARRAY;
                break;
            case is_object($value):
            default:
                return Model_Option::TYPE_OBJECT;
                break;
        }
    }
}