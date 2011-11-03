<?php
/**
 * Grid
 *
 * @category Core
 * @package  Core_Grid
 */
class Core_Grid_Adapter_Select implements Core_Grid_Adapter_AdapterInterface
{
    /**
     * select
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    /**
     * Constructor
     *
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->_select = $data;
    }

    /**
     * order
     *
     * @param string $column
     * @param string $direction
     */
    public function order($column, $direction)
    {
        $this->_select->order($column . ' ' . $direction);
    }

    /**
     * filter
     *
     * @param string $column
     * @param string $filter
     */
    public function filter($column, $filter)
    {
        $this->_select->having($column . ' LIKE ?', str_replace('*', '%', $filter));
    }

    /**
     * get source
     *
     * @return Zend_Db_Select
     */
    public function getSource()
    {
        return $this->_select;
    }
}
