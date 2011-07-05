<?php
/**
 * This is the DbTable class for the options table.
 *
 * @category Application
 * @package Model
 * @subpackage Manager
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 *
 * @version  $Id: Manager.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Options_Model_Options_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'options';

    /** Primary Key */
    protected $_primary = 'id';

    protected $_rowClass = 'Options_Model_Options';

    /**
     * Delete namaspace
     *
     * @param string $namespace
     * @return int
     */
    public function deleteNamespace($namespace)
    {
        $where = $this->getAdapter()->quoteInto('namespace = ?', $namespace);
        return $this->delete($where);
    }

    /**
     * Get options by namespace
     *
     * @param string $namespace
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getByNamespace($namespace)
    {
        $select = $this->select()->where('namespace=?', $namespace);
        return $this->fetchAll($select);
    }

    /**
     * Delete key from namespace
     *
     * @param string $key
     * @param string $namespace
     */
    public function deleteOption($key, $namespace)
    {
        $select = $this->select();
        $select->where('name = ?', $key)->where('namespace = ?', $namespace);

        if ($row = $this->fetchRow($select)) {
            $row->delete();
        }
    }

    /**
     * Set option
     *
     * @param string $key
     * @param mixed  $value
     * @param string $namespace
     * @param string $type
     * @return Options_Model_Options_Row
     */
    public function setOption($key, $value, $namespace = 'default', $type = null)
    {
        $select = $this->select();
        $select->where('name = ?', $key)->where('namespace = ?', $namespace);

        if (!$row = $this->fetchRow($select)) {
            $row = $this->createRow();
            $row->name  = $key;
            $row->namespace = $namespace;
        }
        if ($type) {
            $row->type  = $type;
        }

        $row->value = $value;
        $row->save();

        return $row;
    }

    /**
    * Get option
    *
    * @param string $key
    * @param string $namespace
    * @return Options_Model_Options_Row|null
    */
    public function getOption($key, $namespace)
    {
        $select = $this->select();
        $select->where('name = ?', $key)->where('namespace = ?', $namespace);

        return $this->fetchRow($select);
    }
}