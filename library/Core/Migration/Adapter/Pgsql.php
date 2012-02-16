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
 * Class Core_Migration_Adapter_Pgsql
 *
 * @category Core
 * @package  Core_Migration
 * @subpackage Adapter
 *
 * @author   Alexey Novikov <oleksii.novikov@gmail.com>
 */
class Core_Migration_Adapter_Pgsql extends Core_Migration_Adapter_Abstract
{

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
     * Create table
     *
     * @param string $table
     * @return Core_Migration_Adapter_Abstract
     */
    public function createTable($table)
    {
        $this->query(
            'CREATE TABLE ' .
            $this->getDbAdapter()->quoteIdentifier($table) .
            ' (
               id serial NOT NULL,
                PRIMARY KEY (id)
            )
            WITH (
              OIDS = FALSE
            )'
        );

        return $this;
    }

    /**
     * dropSequence
     *
     * @param string $sequence Sequence name
     * @return Core_Migration_Abstract
     */
    public function dropSequence($sequence)
    {
        $this->query('DROP SEQUENCE ' . $sequence);
    }

    /**
     * dropTable
     *
     * @param   string     $table  table name
     * @return  Core_Migration_Abstract
     */
    public function dropTable($table)
    {
        $this->query('DROP TABLE ' . $this->getDbAdapter()->quoteIdentifier($table));
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

        // alter table $table add column $column $options        

        // ALTER TABLE foo ADD COLUMN test character varying(255) NOT NULL;

        $query = 'ALTER TABLE ' . $this->getDbAdapter()->quoteIdentifier($table) .
            ' ADD COLUMN ' . $this->getDbAdapter()->quoteIdentifier($column);

        // switch statement for $datatype
        switch ($datatype) {
            case Core_Migration_Abstract::TYPE_VARCHAR:
                $length = $length ? $length : 255;
                $query .= " character varying($length)";
                break;
            case Core_Migration_Abstract::TYPE_FLOAT:
                $query .= " double precision";
                break;
            case Core_Migration_Abstract::TYPE_DATETIME:
                $query .= ' timestamp without time zone';
                break;
            case Core_Migration_Abstract::TYPE_LONGTEXT:
                $query .= ' text';
                break;
            case Core_Migration_Abstract::TYPE_TIMESTAMP:
                $query .= ' double precision';
                break;
            case Core_Migration_Abstract::TYPE_ENUM:
                $this->createEnumType($table . '_' . $column, $length);
                $query .= $table . '_' . $column . '_type';
                break;
            default:
                $query .= " $datatype";
                break;
        }

        if (!is_null($default)) {
            // switch statement for $datatype
            switch ($datatype) {
                case Core_Migration_Abstract::TYPE_TIMESTAMP && $default == 'CURRENT_TIMESTAMP':
                    $query .= " default extract(epoch FROM now())";
                    break;
                default:
                    $query .= ' default ' . $this->getDbAdapter()->quote($default);
                    break;
            }
        }

        if ($notnull) {
            $query .= " NOT NULL";
        }

        $this->query($query);

        if ($primary) {
            $this->addConstraintPrimary($table, $column);
        }

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
     * @param array  $columns
     * @param string $indName
     * @return Core_Migration_Adapter_Abstract
     */
    public function createUniqueIndexes($table, array $columns, $indName = null)
    {
        if (!$indName) {
            $indName = strtoupper($table . '_' . implode('_', $columns));
        }

        $indName = $this->getDbAdapter()->quoteIdentifier($indName);

        //quoting a columns
        $quotedColumns = $this->_quoteIdentifierArray($columns);
        $query = 'CREATE UNIQUE INDEX ' . $indName
            . ' ON ' . $this->getDbAdapter()->quoteIdentifier($table)
            . ' (' . $quotedColumns . ')';
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
        if ($indName) {
            $query = 'DROP INDEX IF EXISTS '
                . $this->getDbAdapter()->quoteIdentifier($indName);
            $this->query($query);
        } else {
            throw new Core_Exception(
                "Can't drop index " . $indName . " ON " . $table
            );
        }
        return $this;
    }

    /**
     * Create a foreiggn key constraint
     *
     * @param string $table Local table
     * @param string $name
     * @param string $field
     * @param string $referencesField
     * @param string $referencesTable
     * @param string $onUpdate
     * @param string $onDelete
     * @return Core_Migration_Adapter_Abstract
     */
    public function addConstraintFk(
        $table, $field, $referencesField,
        $referencesTable, $name = null, $onUpdate = 'NO ACTION', $onDelete = 'NO ACTION'
    )
    {

        /*
         ALTER TABLE foo ADD CONSTRAINT foraaa FOREIGN KEY (fortest) REFERENCES activity (user_id)
   ON UPDATE NO ACTION ON DELETE CASCADE;
CREATE INDEX fki_foraaa ON foo(fortest); */

        $query = 'ALTER TABLE ' . $table . ' ADD';

        if (!is_null($name)) {
            $query .= ' CONSTRAINT ' . $name;
        }

        $query = ' FOREIGN KEY (' . $field . ') REFERENCES ' . $referencesTable
            . ' (' . $referencesField . ') '
            . 'ON UPDATE ' . $onUpdate . ' ON DELETE ' . $onDelete;

        $this->query($query);

        return $this;
    }


    /**
     * Create a Primary key constraint
     *
     * @param string $table Table
     * @param string $name  Name
     * @param string $field Field
     * @return Core_Migration_Adapter_Abstract
     */
    public function addConstraintPrimary($table, $column, $name = null)
    {

        $query = "SELECT
                    conname
                  FROM pg_class r, pg_constraint c
                  WHERE
                    r.oid = c.conrelid 
                    AND relname = '" . $table . "'
                    AND contype = 'p'";
        $pkName = $this->getDbAdapter()->fetchOne($query);

        if (!empty($pkName)) { // if we already have Pk
            $query = "SELECT
                    pg_attribute.attname
                FROM pg_index, pg_class, pg_attribute
                WHERE
                    pg_class.oid = '" . $table . "'::regclass
                    AND indrelid = pg_class.oid
                    AND pg_attribute.attrelid = pg_class.oid
                    AND pg_attribute.attnum = any(pg_index.indkey)
                    AND indisprimary";
            $currKeys = $this->getDbAdapter()->fetchCol($query);
            array_push($currKeys, $column);

            $query = 'ALTER TABLE ' . $table . ' DROP CONSTRAINT ' . $pkName;
            $this->query($query);

            $query = 'ALTER TABLE ' . $table . ' ADD CONSTRAINT ' . $pkName
                . ' PRIMARY KEY (' . join(',', $currKeys) . ')';
            $this->query($query);

            return $this;
        } else { // If we have no primary keys
            if (is_null($name)) {
                $name = $table . '_pkey';
            }

            $query = 'ALTER TABLE ' . $table . ' ADD CONSTRAINT ' . $name
                . ' PRIMARY KEY (' . $column . ')';
            $this->query($query);
        }

        return $this;
    }

    /**
     * Create Enum type (for mysql compatability, but can be used directly)
     *
     * @param string $name
     * @param string $values
     * @return Core_Migration_Adapter_Abstract
     */
    public function createEnumType($name, $values)
    {

        if (is_array($values)) {
            // array to string 'el','el',...
            $values = "'" . join("','", $values) . "'";
        }

        $name .= '_type';

        $query = 'CREATE TYPE ' . $name . ' AS ENUM (' . $values . ')';
        $this->query($query);
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