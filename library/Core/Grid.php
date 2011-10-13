<?php
/**
 * Grid
 *
 * @category Core
 * @package  Core_Grid
 */
class Core_Grid extends Core_Grid_Abstract
{
    /**
     * types
     */
    const TYPE_DATA = 'data';
    const TYPE_EMPTY = 'empty';

    /**
     * paginator
     *
     * @var Zend_Paginator
     */
    protected $_paginator = null;

    /**
     * get headers
     *
     * @throws Core_Exception
     * @return array
     */
    public function getHeaders()
    {
        if ($this->_headers === null) {

            /** init paginator if it wasn't initialized */
            $this->getPaginator();

            $this->_headers = array();
            foreach ($this->_columns as $columnId => $column) {
                if (empty($column['name'])) {
                    throw new Core_Exception('Column "' . $columnId . '" does not have name');
                }

                $header = new stdClass();
                $header->id = $columnId;
                $header->name = $column['name'];
                $header->type = $this->_getColumnType($columnId);
                $header->isOrdered = isset($column['order']) && $column['order'] ? true : false;
                $header->orderDirection = isset($column['order']) ? $column['order'] : '';
                $this->_headers[] = $header;
            }
        }

        return $this->_headers;
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
            $items = $this->getPaginator()->getCurrentItems();

            if ($items instanceof Zend_Db_Table_Rowset) {
                $items = $items->toArray();
            }

            $this->_data = array();
            foreach ($items as $item) {
                $row = array();
                foreach ($this->_columns as $id => $column) {
                    $value = '';
                    $type = $this->_getColumnType($id);

                    if ($type === self::TYPE_DATA) {
                        $index = $this->_getColumnIndex($id);

                        if (!isset($item[$index])) {
                            throw new Core_Exception('Index "' . $index . '" does not exist in data source');
                        }

                        $value = $item[$index];
                    }

                    if (isset($column['formatter'])) {
                        $function = '';
                        $formatter = $column['formatter'];
                        if (!is_callable($formatter, null, $function)) {
                            throw new Core_Exception('"' . $function . '" is not callable');
                        }
                        $value = call_user_func($formatter, $value, $item, $column);
                    }
                    $row[$id] = $value;
                }
                $this->_data[] = $row;
            }
        }

        return $this->_data;
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

        /** default ordering */
        if (empty($this->_orders)) {
            if ($this->_defaultOrders) {
                $this->_orders = $this->_defaultOrders;
            } else {
                /** order by first column if default order isn't set */
                $columnId = reset(array_keys($this->_columns));
                $this->setOrder($columnId);
            }
        }

        /** ordering */
        foreach ($this->_orders as $columnId => $direction) {
            $this->_select->order($this->_getColumnIndex($columnId) . ' ' . $direction);
            $this->_columns[$columnId]['order'] = $direction;
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
     * get column
     *
     * @throws Core_Exception
     * @param $columnId
     * @return
     */
    protected function _getColumn($columnId)
    {
        if (empty($this->_columns[$columnId])) {
            throw new Core_Exception('Column "' . $columnId . '" does not exist');
        }

        return $this->_columns[$columnId];
    }

    /**
     * get column index
     *
     * @throws Core_Exception
     * @param $columnId
     * @return string
     */
    protected function _getColumnIndex($columnId)
    {
        $column = $this->_getColumn($columnId);

        if (empty($column['index'])) {
            throw new Core_Exception('Index of column "' . $columnId . '" does not exist');
        }

        return $column['index'];
    }

    /**
     * get column type
     *
     * @throws Core_Exception
     * @param $columnId
     * @return string
     */
    protected function _getColumnType($columnId)
    {
        $column = $this->_getColumn($columnId);

        $type = 'empty';
        if (isset($column['type'])) {
            $type = $column['type'];
        }

        if (!in_array($type, $this->_getAllowedTypes())) {
            throw new Core_Exception('Type "' . $type . '" of column "' . $columnId . '" is not allowed');
        }

        return $type;
    }

    /**
     * get allowed types
     *
     * @return array
     */
    protected function _getAllowedTypes()
    {
        return array(
            self::TYPE_DATA,
            self::TYPE_EMPTY
        );
    }
}
