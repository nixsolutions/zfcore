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
 * @category Core
 * @package  Core_Category
 */
class Core_Categories_Row extends Zend_Db_Table_Row_Abstract
{
    /**
     * @var integer|string
     */
    protected $_parentNodeKey = 'parentId';

    /**
     * @var integer|string
     */
    protected $_nodeKey = 'id';

    /**
     * @var Zend_Db_Table_Row_Abstract
     */
    protected $_parent;

    /**
     * @var Zend_Db_Table_Rowset_Abstract
     */
    protected $_children;

    /**
     * Add child
     *
     * @param \Core_Categories_Row|self $row
     * @throws Zend_Db_Table_Row_Exception
     * @return self
     */
    public function addChild(self $row)
    {
        if (empty($this->_cleanData)) {
            throw new Zend_Db_Table_Row_Exception('Parent category is not created yet');
        }
        if ($row->getParentNodeId() != $this->getNodeId()) {
            $row->setParentNode($this->getNodeId());
        }

        $this->getChildren()->addRow($row);

        return $this;
    }

    /**
     * Get Parent
     *
     * @return Zend_Db_Table_Row_Abstract|null
     */
    public function getParent()
    {
        if (!$this->_parent && $this->getParentNodeId()) {
            $this->_parent = $this->getTable()->find($this->getParentNodeId())
                ->current();
        }
        return $this->_parent;
    }

    /**
     * Add child
     *
     * @param self $row
     * @throws Zend_Db_Table_Row_Exception
     */
    public function getChildren()
    {
        if (!$this->_children) {
            if (empty($this->_cleanData)) {
                throw new Zend_Db_Table_Row_Exception(
                    'Parent category is not created yet'
                );
            }

            $select = $this->select();
            $select->where($this->_parentNodeKey . '=?', $this->getNodeId());

            $this->_children = $this->getTable()->fetchAll($select);
        }
        return $this->_children;
    }

    /**
     * Get parent node key
     *
     * @return integer|string
     */
    public function getParentNodeId()
    {
        return $this->{$this->_parentNodeKey};
    }

    /**
     * Get parent node ID
     *
     * @return self|integer|string
     * @return self
     */
    public function setParentNode($node)
    {
        if ($node instanceof self) {
            $node = $node->getNodeId();
        }
        $this->__set($this->_parentNodeKey, $node);
        $this->save();

        return $this;
    }

    /**
     * Get node ID
     *
     * Node ID is not primaryKey
     *
     * @return integer|string
     */
    public function getNodeId()
    {
        return $this->{$this->_nodeKey};
    }
}