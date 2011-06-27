<?php
/**
 * Class for SQL table interface.
 *
 * @category   Core
 * @package    Core_Db
 * @subpackage Table
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 *
 * @version  $Id: Abstract.php 159 2010-07-09 12:12:28Z AntonShevchuk $
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
     * Return Primary Key
     *
     * @return string
     */
    public function getPrimary()
    {
        return $this->_primary;
    }

    /**
     * Fetches a new blank row (not from the database).
     *
     * @param  array $data OPTIONAL data to populate in the new row.
     * @param  string $defaultSource OPTIONAL flag to force default values
     * @return Zend_Db_Table_Row_Abstract
     */
    public function create(array $data = array(), $defaultSource = null)
    {
        return parent::createRow($data, $defaultSource);
    }

    /**
     * get row by id
     *
     * @param  integer $id  primary key
     * @return
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
        return $this->getById($id)->delete();
    }

    /**
     * Unexistent methods handler
     *
     * @param string $name
     * @param mixed $arguments
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
     *        'vasya@mail.ru'
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
     * @param array $values
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
                throw new Core_Exception('No such condition must be OR or ' .
                                         'AND, got '.$param);
            }
        }
        return $select;
    }
}