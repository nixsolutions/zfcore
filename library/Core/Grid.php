<?php
/**
 * Grid
 *
 * @category Core
 * @package  Core_Grid
 */
class Core_Grid
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
     * select
     *
     * @var Zend_Db_Select
     */
    protected $_select = null;

    /**
     * paginator
     *
     * @var Zend_Paginator
     */
    protected $_paginator = null;

    /**
     * orders
     *
     * @var array
     */
    protected $_orders = array();

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

    }

    /**
     * add column
     *
     * @param $columnId
     * @param array $options
     * @return Core_Grid
     */
    public function addColumn($columnId, array $options)
    {
        $this->_columns[$columnId] = $options;
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
     * get headers
     *
     * @throws Core_Exception
     * @return array
     */
    public function getHeaders()
    {
        if ($this->_headers === null) {
            if (empty($this->_paginator)) {
                $this->_buildPaginator();
            }

            $this->_headers = array();
            foreach ($this->_columns as $columnId => $column) {
                if (empty($column['name'])) {
                    throw new Core_Exception('Column ' . $columnId . ' does not have name');
                }

                $header = new stdClass();
                $header->id = $columnId;
                $header->name = $column['name'];
                $header->isOrdered = isset($column['order']) && $column['order'] ? true : false;
                $header->orderDirection = isset($column['order']) ? $column['order'] : '';
                $this->_headers[] = $header;
            }
        }

        return $this->_headers;
    }

    /**
     * get paginator
     *
     * @return Zend_Paginator
     */
    public function getPaginator()
    {
        if (empty($this->_paginator)) {
            $this->_buildPaginator();
        }

        return $this->_paginator;
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
     * @param $columnId
     * @param string $direction
     * @return Core_Grid
     */
    public function setOrder($columnId, $direction = 'ASC')
    {
        $this->_orders[$columnId] = strtoupper($direction);
        return $this;
    }

    /**
     * get data
     *
     * @throws Core_Exception
     * @return array
     */
    public function getData()
    {
        if ($this->_data === null) {
            if (empty($this->_paginator)) {
                $this->_buildPaginator();
            }

            $items = $this->_paginator->getCurrentItems();

            if ($items instanceof Zend_Db_Table_Rowset) {
                $items = $items->toArray();
            }

            $this->_data = array();
            foreach ($items as $item) {
                $row = array();
                foreach ($this->_columns as $id => $column) {
                    $index = $this->_getColumnIndex($id);
                    $value = isset($item[$index]) ? $item[$index] : '';
                    if (isset($column['formatter'])) {
                        $function = '';
                        $formatter = $column['formatter'];
                        if (!is_callable($formatter, null, $function)) {
                            throw new Core_Exception($function . ' is not callable');
                        }
                        $value = call_user_func($formatter, $value, $item);
                    }
                    $row[$id] = $value;
                }
                $this->_data[] = $row;
            }
        }

        return $this->_data;
    }

    /**
     * build paginator
     *
     * @throws Core_Exception
     * @return void
     */
    protected function _buildPaginator()
    {
        if (empty($this->_select)) {
            throw new Core_Exception('Select is not set');
        }

        if (empty($this->_columns)) {
            throw new Core_Exception('There are no any columns');
        }

        /** ordering */
        if ($this->_orders) {
            foreach ($this->_orders as $columnId => $direction) {
                $this->_select->order($this->_getColumnIndex($columnId) . ' ' . $direction);
                $this->_columns[$columnId]['order'] = $direction;
            }
        }

        /** filtering */
        if ($this->_filters) {
            foreach ($this->_filters as $columnId => $filters) {
                $index = $this->_getColumnIndex($columnId);
                foreach ($filters as $filter) {
                    $this->_select->having($index . ' LIKE ?', '%' . $filter . '%');
                }
            }
        }

        if (empty($this->_itemCountPerPage)) {
            throw new Core_Exception('Item count per page is not set');
        }

        if (empty($this->_currentPageNumber)) {
            throw new Core_Exception('Current page is not set');
        }

        $this->_paginator = Zend_Paginator::factory($this->_select)
            ->setItemCountPerPage($this->_itemCountPerPage)
            ->setCurrentPageNumber($this->_currentPageNumber);
    }

    /**
     * set select
     *
     * @param Zend_Db_Select $select
     * @return Core_Grid
     */
    public function setSelect(Zend_Db_Select $select)
    {
        $this->_select = $select;
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
     * get column index
     *
     * @throws Core_Exception
     * @param $columnId
     * @return
     */
    protected function _getColumnIndex($columnId)
    {
        if (empty($this->_columns[$columnId])) {
            throw new Core_Exception('Column ' . $columnId . ' does not exist');
        }

        if (empty($this->_columns[$columnId]['index'])) {
            throw new Core_Exception('Index of column ' . $columnId . ' does not exist');
        }

        return $this->_columns[$columnId]['index'];
    }
}
