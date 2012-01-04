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
        $this->_select->order( $column . ' ' . $direction );
    }

    /**
     * filter
     *
     * @param string $column
     * @param string $filter
     */
    public function filter($column, $filter)
    {
        $this->_select->having( $column . ' LIKE ?', str_replace( '*', '%', $filter ) );
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
