<?php
/**
 * Class for SQL table row interface.
 *
 * @category   Core
 * @package    Core_Db
 * @subpackage Table
 * 
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * 
 * @version  $Id: Abstract.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Core_Db_Table_Row_Abstract 
    extends Zend_Db_Table_Row_Abstract
{
    /**
     * Constructor
     *
     * @param  array $config OPTIONAL Array of user-specified config options.
     * @return void
     * @throws Zend_Db_Table_Row_Exception
     */
    public function __construct(array $config = array())
    {
        $this->_tableClass = get_class($this) .'_Table';
        parent::__construct($config);
    }
    
    /**
     * Init Row
     *
     * @return void
     */
    public function init()
    {  
        $cols = $this->getTable()->info(Zend_Db_Table::COLS);
        
        foreach ($cols as $col) {
            if (!isset($this->_data[$col])) {
                $this->_data[$col] = null;
            }
        }
    }
    
}