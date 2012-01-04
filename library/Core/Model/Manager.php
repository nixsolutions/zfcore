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
 * Class Core_Model_Manager - just mapper for model
 *
 * @category Core
 * @package  Core_Model
 *
 * @author   Anton Shevchuk
 * @created  Tue Apr 20 08:17:02 GMT 2010
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
        if (is_string( $dbTable )) {
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
            $dbTable = $this->getModelName() . '_Table';
            $this->setDbTable( $dbTable );
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
            $modelName = get_class( $this );
            $modelName = substr( $modelName, 0, strpos( $modelName, '_Manager' ) );
            $this->setModelName( $modelName );
        }

        return $this->_modelName;
    }
}