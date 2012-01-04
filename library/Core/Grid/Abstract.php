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
 * Abstract Grid
 *
 * @category Core
 * @package  Core_Grid
 */
abstract class Core_Grid_Abstract
{
    /**
     * columns
     *
     * @var array
     */
    protected $_columns = array();

    /**
     * headers
     *
     * @var array
     */
    protected $_headers = null;

    /**
     * data
     *
     * @var array
     */
    protected $_data = null;

    /**
     * adapter
     *
     * @var Core_Grid_Adapter_AdapterInterface
     */
    protected $_adapter = null;

    /**
     * orders
     *
     * @var array
     */
    protected $_orders = array();

    /**
     * default orders
     *
     * @var array
     */
    protected $_defaultOrders = array();

    /**
     * filters
     *
     * @var array
     */
    protected $_filters = array();

    /**
     * item count per page
     *
     * @var int
     */
    protected $_itemCountPerPage = null;

    /**
     * current page
     *
     * @var int
     */
    protected $_currentPageNumber = null;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * init
     *
     * @return void
     */
    public function init()
    {

    }

    /**
     * add column
     *
     * @param       $columnId
     * @param array $options
     * @return Core_Grid
     */
    public function setColumn($columnId, array $options)
    {
        if (empty($this->_columns[$columnId])) {
            $this->_columns[$columnId] = array();
        }
        $this->_columns[$columnId] = array_merge(
            $this->_columns[$columnId],
            $options
        );

        return $this;
    }

    /**
     * remove column
     *
     * @param $columnId
     * @return Core_Grid
     */
    public function removeColumn($columnId)
    {
        if (isset($this->_columns[$columnId])) {
            unset($this->_columns[$columnId]);
        }
        return $this;
    }

    /**
     * set filter
     *
     * @param $columnId
     * @param $filter
     * @return Core_Grid
     */
    public function setFilter($columnId, $filter)
    {
        isset($this->_filters[$columnId]) || $this->_filters[$columnId] = array();

        $this->_filters[$columnId][] = $filter;
        return $this;
    }

    /**
     * set ordering
     *
     * @param        $columnId
     * @param string $direction
     * @return Core_Grid
     */
    public function setOrder($columnId, $direction = 'ASC')
    {
        $this->_orders[$columnId] = strtoupper( $direction );
        return $this;
    }

    /**
     * set default ordering
     *
     * @param        $columnId
     * @param string $direction
     * @return Core_Grid
     */
    public function setDefaultOrder($columnId, $direction = 'ASC')
    {
        $this->_defaultOrders[$columnId] = strtoupper( $direction );
        return $this;
    }

    /**
     * set adapter
     *
     * @param Core_Grid_Adapter_AdapterInterface $adapter
     * @return Core_Grid
     */
    public function setAdapter(Core_Grid_Adapter_AdapterInterface $adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * set item count per page
     *
     * @param $count
     * @return Core_Grid
     */
    public function setItemCountPerPage($count)
    {
        $this->_itemCountPerPage = $count;
        return $this;
    }

    /**
     * get item count per page
     *
     * @return int
     */
    public function getItemCountPerPage()
    {
        return $this->_itemCountPerPage;
    }

    /**
     * set current page number
     *
     * @param $page
     * @return Core_Grid
     */
    public function setCurrentPageNumber($page)
    {
        $this->_currentPageNumber = $page;
        return $this;
    }

    /**
     * get current page number
     *
     * @return int
     */
    public function getCurrentPageNumber()
    {
        return $this->_currentPageNumber;
    }

    /**
     * get headers
     *
     * @abstract
     * @return array
     */
    abstract public function getHeaders();

    /**
     * get data
     *
     * @abstract
     * @return array
     */
    abstract public function getData();
}
