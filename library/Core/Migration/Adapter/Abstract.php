<?php
/**
 * Copyright (c) 2012 by PHP Team of NIX Solutions Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Class Core_Migration_Abstract
 *
 * abstract migration
 *
 * @category Core
 * @package  Core_Migration
 * @subpackage Adapter
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
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
     * @return Core_Migration_Adapter_Abstract
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
     * @param array        $bind
     * @return  Core_Migration_Abstract
     */
    public function query($query, $bind = array())
    {
        $this->getDbAdapter()->query($query, $bind);
        return $this;
    }

    /**
     * Insert
     *
     * @param string $table
     * @param array  $params
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
     * @param array  $columns
     * @param string $indName
     * @return Core_Migration_Adapter_Abstract
     */
    abstract public function createUniqueIndexes($table, array $columns, $indName = null);

    /**
     * Drop an index on table
     *
     * @param        $table
     * @param string $indName
     * @return Core_Migration_Adapter_Abstract
     */
    abstract public function dropUniqueIndexes($table, $indName);

    /**
     * __call for unsupported adaptors methods
     *
     * @param string $name      Method name
     * @param mixed  $arguments Method Arguments
     *                          return Core_Migration_Adapter_Abstract
     * @return \Core_Migration_Adapter_Abstract
     */
    public function  __call($name, $arguments)
    {
        return $this;
    }

    /**
     * setForeignKeysChecks
     *
     * @param  bool $flag
     * @return Core_Migration_Adapter_Abstract
     */
    abstract public function setForeignKeysChecks($flag);
}
