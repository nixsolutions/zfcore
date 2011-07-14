<?php
/**
 * Core_Categories_Row
 *
 * @version $Id$
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
     * @param self $row
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