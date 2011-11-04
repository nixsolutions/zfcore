<?php
/**
 * Grid
 *
 * @category Core
 * @package  Core_Grid
 */
class Core_Grid_Adapter_Array implements Core_Grid_Adapter_AdapterInterface
{
    /**
     * data
     *
     * @var array
     */
    protected $_data;

    /**
     * cmpFunction
     *
     * @var string
     */
    protected $_cmpFunction = 'strcmp';

    /**
     * Constructor
     *
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->_data = (array) $data;
    }

    /**
     * order
     *
     * @param string $column
     * @param string $direction
     */
    public function order($column, $direction)
    {
        $direction = strtolower($direction);

        uasort($this->_data, array($this, "sort_{$column}_{$direction}"));
    }

    /**
     * Call
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (0 === strpos($method, 'sort_')) {
            $method = explode('_', $method);
            array_unshift($args, $method['1'], $method['2']);

            return call_user_func_array(array($this, 'sort'), $args);
        }
    }

    /**
     * Set comparision function
     *
     * @param string $functionName
     */
    public function setCmpFunction($functionName)
    {
        $this->_cmpFunction = (string) $functionName;
    }


    /**
     * Sort
     *
     * @param string $column
     * @param string $direction
     * @param array $a
     * @param array $b
     * @return boolen
     */
    public function sort($column, $direction, $a, $b)
    {
        $result = (bool) call_user_func(
            $this->_cmpFunction,
            $a[$column],
            $b[$column]
        );
        if ('desc' == $direction) {
            $result = !$result;
        }
        return $result;
    }

    /**
     * filter
     *
     * @param string $column
     * @param string $filter
     */
    public function filter($column, $filter)
    {
        $filter = preg_quote($filter);
        foreach ($this->_data as $i => $row) {
            if (!empty($row[$column])) {
                if (preg_match('/' . $filter . '/im', $row[$column])) {
                    continue;
                }
            }
            unset($this->_data[$i]);
        }
    }

    /**
     * get source
     *
     * @return Zend_Db_Select
     */
    public function getSource()
    {
        return $this->_data;
    }
}
