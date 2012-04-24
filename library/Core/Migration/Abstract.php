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
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 */
abstract class Core_Migration_Abstract
{
    const TYPE_INT = 'int';
    const TYPE_BIGINT = 'bigint';

    const TYPE_FLOAT = 'float';

    const TYPE_TEXT = 'text';
    const TYPE_LONGTEXT = 'longtext';

    const TYPE_VARCHAR = 'varchar';
    const TYPE_ENUM = 'enum';

    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_TIME = 'time';
    const TYPE_TIMESTAMP = 'timestamp';

    /**
     * Default Database adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_dbAdapter = null;

    /**
     * migration Adapter
     *
     * @var Core_Migration_Adapter_Abstract
     */
    protected $_migrationAdapter = null;

    /**
     * migration manager
     *
     * @var Core_Migration_Manager
     */
    protected $_migrationManager = null;

    /**
     * up
     *
     * update DB from migration
     *
     * @return  Core_Migration_Abstract
     */
    abstract public function up();

    /**
     * down
     *
     * degrade DB from migration
     *
     * @return  Core_Migration_Abstract
     */
    abstract public function down();

    /**
     * getDescription
     *
     * get migration description
     *
     * @return string
     */
    public function getDescription()
    {
        return '';
    }


    /**
     * setDbAdapter
     *
     * @param  Zend_Db_Adapter_Abstract $dbAdapter
     * @return Core_Migration_Abstract
     */
    public function setDbAdapter($dbAdapter = null)
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
     * setMigrationMananger
     *
     * @param \Core_Migration_Manager $migrationManager
     * @return Core_Migration_Abstract
     */
    public function setMigrationMananger(Core_Migration_Manager $migrationManager)
    {
        $this->_migrationManager = $migrationManager;
        return $this;
    }

    /**
     * getMigrationManager
     *
     * @return Core_Migration_Manager
     */
    public function getMigrationManager()
    {
        if (!$this->_migrationManager) {
            throw new Core_Exception('Migration manager is not set');
        }
        return $this->_migrationManager;
    }

    /**
     * setMigrationAdapter
     *
     *
     * @return Core_Migration_Abstract
     */
    public function setMigrationAdapter()
    {
        if ($this->getDbAdapter() instanceof Zend_Db_Adapter_Pdo_Mysql) {
            $className = 'Core_Migration_Adapter_Mysql';
        } elseif ($this->getDbAdapter() instanceof Zend_Db_Adapter_Pdo_Sqlite) {
            $className = 'Core_Migration_Adapter_Sqlite';
        } elseif ($this->getDbAdapter() instanceof Zend_Db_Adapter_Pdo_Pgsql) {
            $className = 'Core_Migration_Adapter_Pgsql';
        } else {
            throw new Core_Exception("This type of adapter not suppotred");
        }
        $this->_migrationAdapter = new $className($this->getDbAdapter());

        return $this;
    }

    /**
     * getMigrationAdapter
     *
     * @return Core_Migration_Adapter_Abstract
     */
    public function getMigrationAdapter()
    {
        if (!$this->_migrationAdapter) {
            $this->setMigrationAdapter();
        }
        return $this->_migrationAdapter;
    }

    /**
     * stop
     *
     * @throws Exception
     */
    public function stop()
    {
        throw new Core_Exception('This is final migration');
    }

    /**
     * query
     *
     * @param  string $query
     * @param  array  $bind
     * @return Core_Migration_Abstract
     */
    public function query($query, $bind = array())
    {
        $this->getMigrationAdapter()->query($query, $bind);
        return $this;
    }

    /**
     * insert
     *
     * @param   string     $table
     * @param   array      $params
     * @return  int The number of affected rows.
     */
    public function insert($table, array $params)
    {
        $this->getMigrationAdapter()->insert($table, $params);

        return $this;
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
        $this->getMigrationAdapter()->update($table, $bind, $where);

        return $this;
    }

    /**
     * createTable
     *
     * @param   string $table table name
     * @return  Core_Migration_Abstract
     */
    public function createTable($table)
    {
        $this->getMigrationAdapter()->createTable($table);

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
        $this->getMigrationAdapter()->dropTable($table);

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
     * @return  Core_Migration_Abstract
     */
    public function createColumn(
        $table,
        $column,
        $datatype,
        $length = null,
        $default = null,
        $notnull = false,
        $primary = false
    )
    {

        //if ($default && self::DEFAULT_CURRENT_TIMESTAMP == $default) {
        //    $default = $this->getMigrationAdapter()->getCurrentTimestamp();
        //}
        $this->getMigrationAdapter()->createColumn(
            $table,
            $column,
            $datatype,
            $length,
            $default,
            $notnull,
            $primary
        );

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
        $this->getMigrationAdapter()->dropColumn($table, $name);

        return $this;
    }

    /**
     * createUniqueIndexes
     *
     * @param   string   $table
     * @param   array    $columns
     * @param   string   $indName
     * @return  bool
     */
    public function createUniqueIndexes($table, array $columns, $indName = null)
    {
        $this->getMigrationAdapter()
            ->createUniqueIndexes($table, $columns, $indName);

        return $this;
    }

    /**
     * dropColumn
     *
     * @param   string   $table
     * @param            $indName
     * @internal param array $columns
     * @return  Core_Migration_Abstract
     */
    public function dropUniqueIndexes($table, $indName)
    {
        $this->getMigrationAdapter()->dropUniqueIndexes($table, $indName);

        return $this;
    }

    /**
     * message
     *
     * output message to console
     *
     * @param   string     $message
     * @return  Core_Migration_Abstract
     */
    public function message($message)
    {
        echo $message . "\n";
        return $this;
    }
}