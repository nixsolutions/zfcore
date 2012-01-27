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
 * Grid
 *
 * @category Core
 * @package  Core_Grid
 * @subpackage Adapter
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
    protected $_cmpFunction = 'strnatcmp';

    /**
     * Constructor
     *
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->_data = (array)$data;
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
     * @param array  $args
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
     * @param array  $a
     * @param array  $b
     * @return boolen
     */
    public function sort($column, $direction, $a, $b)
    {
        $result = call_user_func(
            $this->_cmpFunction,
            $a[$column],
            $b[$column]
        );
        if ('desc' == $direction) {
            $result = -$result;
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
