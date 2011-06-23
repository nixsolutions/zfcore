<?php
/**
 * Class Options_Model_Options_Manager
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
class Options_Model_Options_Manager extends Core_Model_Manager
{
    const TYPE_INT    = 'int';
    const TYPE_FLOAT  = 'float';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY  = 'array';
    const TYPE_OBJECT = 'object';
    
    static protected $_instance = null;
    
    protected $_modelName = 'Options_Model_Options_Manager';
    protected $_cache   = array();
    
    
    /**
     * getInstance
     *
     * @return  Options_Model_Options_Manager
     */
    static public function getInstance() 
    {
        if (self::$_instance === null) {
            self::$_instance = new Options_Model_Options_Manager();
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
        $self = Options_Model_Options_Manager::getInstance();
        
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
        $self = Options_Model_Options_Manager::getInstance();
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
     * @return  Options_Model_Options_Manager
     */
    static public function set($key, $value, $namespace = 'default', $type = null) 
    {
        $self = Options_Model_Options_Manager::getInstance();
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
     * @return  Options_Model_Options_Manager
     */
    static public function delete($key, $namespace = 'default') 
    {
        $self = Options_Model_Options_Manager::getInstance();
        
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
        $self = Options_Model_Options_Manager::getInstance();
        
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
     * @return  Options_Model_Options_Manager
     */
    static public function setNamespace($namespace, array $options) 
    {
        // foreach loop for $options array
        foreach ($options as $key => $value) {
            Options_Model_Options_Manager::set($key, $value, $namespace);
        }
        return Options_Model_Options_Manager::getInstance();
    }
    
    /**
     * deleteNamespace
     *
     * delete all data from namespace
     *
     * @param   string     $namespace
     * @return  Options_Model_Options_Manager
     */
    static public function deleteNamespace($namespace) 
    {
        $self = Options_Model_Options_Manager::getInstance();
        
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
            case Options_Model_Options_Manager::TYPE_INT:
                return (int)$row->value;
                break;
            case Options_Model_Options_Manager::TYPE_FLOAT:
                return floatval($row->value);
                break;
            case Options_Model_Options_Manager::TYPE_STRING:
                return $row->value;
                break;
            case Options_Model_Options_Manager::TYPE_ARRAY:
            case Options_Model_Options_Manager::TYPE_OBJECT:
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
            case Options_Model_Options_Manager::TYPE_INT:
            case Options_Model_Options_Manager::TYPE_FLOAT:
                return "$value";
                break;
            case Options_Model_Options_Manager::TYPE_STRING:
                return "$value";
                break;
            case Options_Model_Options_Manager::TYPE_ARRAY:
            case Options_Model_Options_Manager::TYPE_OBJECT:
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
                return Options_Model_Options_Manager::TYPE_INT;
                break;
            case is_float($value):
                return Options_Model_Options_Manager::TYPE_FLOAT;
                break;
            case is_string($value):
                return Options_Model_Options_Manager::TYPE_STRING;
                break;
            case is_array($value):
                return Options_Model_Options_Manager::TYPE_ARRAY;
                break;
            case is_object($value):
            default:
                return Options_Model_Options_Manager::TYPE_OBJECT;
                break;
        }
    }
}
