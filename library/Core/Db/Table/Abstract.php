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
 * Class for SQL table interface.
 *
 * @category   Core
 * @package    Core_Db
 * @subpackage Table
 *
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link       http://anton.shevchuk.name
 */
class Core_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    /**
     * Set default Row class
     *
     * @var string
     */
    protected $_rowClass = 'Core_Db_Table_Row_Abstract';

    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'Core_Db_Table_Rowset_Abstract';

    /**
     * Return Primary Key
     *
     * @return array
     */
    public function getPrimary()
    {
        return $this->_primary;
    }

    /**
     * Get row by id
     *
     * @param  mixed $id primary key
     * @return \Zend_Db_Table_Row_Abstract
     */
    public function getById($id)
    {
        return $this->find($id)->current();
    }

    /**
     * deleteById
     *
     *
     * @param  integer|array $id  primary key
     * @return integer The number of rows deleted.
     */
    public function deleteById($id)
    {
        if ($row = $this->getById($id)) {
            return $row->delete();
        }
        return null;
    }

    /**
     * Unexistent methods handler
     *
     * @param string $name
     * @param mixed  $arguments
     * @return bool|\Model_User|null
     */
    public function __call($name, $arguments)
    {
        //handles get by dynamic finder like getByNameAndPasswordOrDate()
        if (strpos($name, 'getBy') === 0) {
            return $this->__getByColumnsFinder(str_replace('getBy', '', $name), $arguments);
        } else {
            return false;
        }
    }

    /**
     * getByColumnsFinder
     *
     * <code>
     *    $this->getByLoginOrPasswordAndEmail(
     *        'vasya',
     *        md5(123456),
     *        'vasya
     * @mail             .ru'
     *    )
     * </code>
     *
     * <code>
     *    //fields like UserLogin => Userlogin
     *    //fields like user_login => User_login
     *    $this->getByUser_loginOrUser_passwordAndUser_email(
     *        'vasya',
     *        md5(123456),
     *        'vasya@mail.ru'
     *    )
     * </code>
     *
     * @param string $query
     * @param array  $values
     * @return null | Model_User
     */
    private function __getByColumnsFinder($query, $values)
    {
        if ($params = $this->__parseQuery($query)) {
            $select = $this->__buildSelect($params, $values);
            return $this->fetchRow($select);
        }
        return null;
    }

    /**
     * Parse query to array
     *
     * @param string $query
     * @return array
     */
    private function __parseQuery($query)
    {
        if (preg_match_all('/[A-Z][^A-Z]+/', $query, $matches)) {
            return array_map('strtolower', $matches['0']);
        }
        return false;
    }

    /**
     * Build Zend_Db_Table_Select object
     *
     * @param array $params
     * @param array $values
     * @throws Core_Exception
     * @return object Zend_Db_Table_Select
     */
    private function __buildSelect($params, $values)
    {
        $select = $this->select();

        $fields = $this->info(Zend_Db_Table_Abstract::COLS);
        $fields = array_map('strtolower', $fields);

        $condition = '';

        foreach ($params as $param) {
            if (in_array($param, $fields)) {
                if ($value = array_shift($values)) {
                    if ($value instanceof Zend_Db_Expr) {
                        $value = $value->__toString();
                    }
                    if ($condition == 'or') {
                        $select->orWhere($param . '=?', $value);
                    } else {
                        $select->where($param . '=?', $value);
                    }
                } else {
                    throw new Core_Exception('No value for field ' . $param);
                }
            } elseif (in_array($param, array('or', 'and'))) {
                $condition = $param;
            } else {
                throw new Core_Exception(
                    'No such condition must be OR or ' .
                    'AND, got ' . $param
                );
            }
        }
        return $select;
    }
}