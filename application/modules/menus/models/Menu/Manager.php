<?php
/**
 * @see Core_Model_Manager
 */
require_once 'Core/Model/Manager.php';

/**
 * Menus_Model_Menu_Manager
 *
 * @category    Application
 * @package     Menus_Menus_Model_Menu
 * @subpackage  Manager
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @copyright   Copyright (c) 2010 NIX Solutions (http://www.nixsolutions.com)
 */
class Menus_Model_Menu_Manager extends Core_Model_Manager
{
    /**
     * Model table instance
     *
     * @var null|Menus_Model_Menu_Table
     */
    protected $_dbTable = null;

    /**
     * Model class name
     *
     * @var string
     */
    protected $_modelName = 'Menus_Model_Menu';

    /**
     * Array of menu tree
     *
     * @var null|array
     */
    protected $_menuArray = null;

    /**
     * Menu tree
     *
     * @var null|Menus_Model_Menu
     */
    protected $_menuTableRowset = null;

    public function __construct()
    {
        $this->setDbTable('Menus_Model_Menu_Table');
        $this->setMenuRowset();
    }

    /**
     * Get parent menu option for form selectbox
     *
     * @return mixed
     */
    public function getMenuItemsForEditForm() {
        static $parentMenus;

        if (!empty($parentMenus)) {
            return $parentMenus;
        }

        $parentMenus = array(0 => 'No parent');

        $this->setMenuRowset();

        foreach ($this->_menuTableRowset as $menuItem) {
            $parentMenus[$menuItem->id] = $menuItem->label;
        }

        return $parentMenus;
    }

    public function getTargetOptionsForEditForm() {
        return $parentMenus = array(
            0 => 'Don\'t set',
            1 => Menus_Model_Menu::TARGET_BLANK,
            2 => Menus_Model_Menu::TARGET_PARENT,
            3 => Menus_Model_Menu::TARGET_SELF,
            4 => Menus_Model_Menu::TARGET_TOP
        );
    }

    public function getTypeOptionsForEditForm() {
        return $parentMenus = array(
            Menus_Model_Menu::TYPE_URI => 'URI',
            Menus_Model_Menu::TYPE_MVC => 'MVC'
        );
    }

    public function getMenuById($id)
    {
        $menuArray = $this->getMenuArray();
        return $this->getArrayItemByKey($menuArray, 'id', $id);
    }

    public function getMenuByLabel($label = null)
    {
        $menuArray = $this->getMenuArray();
        return $this->getArrayItemByKey($menuArray, 'label', $label);
    }

    public function getArrayItemByKey ($arr, $name, $value)
    {
        if (!is_array($arr)) {
            return null;
        }

        foreach ($arr as $key => $val) {

            if (is_array($val)) {
                $results = $this->getArrayItemByKey ($val, $name, $value);
            } else {
                if ($key == $name && $value == $val) {
                    return $arr;
                }
            }

            if (is_array($results)) {
                 break;
            }
        }

        return $results;
    }

    protected function makeParentChildRelations($inputArray)
    {

        if (!is_array($inputArray)) {
            return null;
        }

        $outputArray = array();

        foreach ($inputArray as $entry) {
            $entry['pages'] = array();
            $id = $entry['id'];

            if ($entry['parent'] == '') {
                $all[$id] = $entry;
                $outputArray[] = &$all[$id];

            } else {
                $dangling[$id] = $entry;
            }
        }

        while (count($dangling) > 0) {
            foreach($dangling as $entry) {
                $id = $entry['id'];
                $pid = $entry['parent'];

                if (isset($all[$pid])) {
                    $all[$id] = $entry;
                    $all[$pid]['pages'][] =& $all[$id];
                    unset($all[$id]['parent']);
                    unset($dangling[$entry['id']]);
                }
            }
        }

        return $outputArray;
    }

    /**
     * @return void
     */
    protected function setMenuArray()
    {
        if (null === $this->_menuArray) {
            if ($this->_menuTableRowset instanceof Zend_Db_Table_Rowset) {
                $this->_menuArray = $this->_menuTableRowset->toArray();

                $this->_menuArray = $this->makeParentChildRelations($this->_menuArray);
            } else {
                $this->_menuArray = array();
            }
        }
    }

    /**
     * Return _menuArray
     *
     * @return array
     */
    public function getMenuArray()
    {
        $this->setMenuArray();
        return $this->_menuArray;
    }

    /**
     * Return _menuArray->toArray()
     *
     * @return array
     */
    public function getRawMenuArray()
    {
        return $this->_menuTableRowset->toArray();
    }

    /**
     * @return void
     */
    public function setMenuRowset()
    {
        if (null === $this->_menuTableRowset) {
            $this->_menuTableRowset = $this->_dbTable->fetchAll();
        }
    }

    /**
     * Return _menuRowset
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getMenuRowset()
    {
        return $this->_menuTableRowset;
    }

    public function getMenuRows()
    {
        if (null === $this->_dbTable) {
            return;
        }
        return $this->getDbTable()->fetchAll()->toArray();
    }

    public function getMenus()
    {

        $this->getDbTable()->fetchAll();
        $select = $this->select()->from($this->getDbTable(), array('parent_id'))->distinct();
        $result = $this->fetchAll($select);

        if ($result) {
            foreach ($result as $row) {
                $ids[] = $row->parent_id;
            }
            return $this->find($ids);
        }

        return $this->getTable()->fetchAll();
    }

    public function getModules()
    {
        return array('default', 'admin');
    }

    public function getControllersByModuleName($moduleName = 'default')
    {
        return array(
            array(array('controller' => 'index',"label" => "index", 'name' => 'index'),
            array('controller' => 'index2',"label" => "index2", 'name' => 'index3'))
        );
    }

    public function getActionsByControllerName($controllerName = 'index')
    {
       return array(
            array(array('controller' => 'index',"label" => "index", 'name' => 'index'),
            array('controller' => 'index2',"label" => "index2", 'name' => 'index3'))
        );
    }

    public function getRowById($id)
    {
        $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $id);
        $row = $this->getDbTable()->fetchRow($this->getDbTable()->select()->where($where));
        return $row;
    }

    public function removeById($id) {
        $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $id);
        $this->getDbTable()->delete($where);
        return true;
    }

    public function addMenuItem($formData = null)
    {
        if (!empty($formData) && is_array($formData)) {
            $dbTable = $this->getDbTable();

            $label = $formData['label'];
            $parent = (integer) $formData['parent'];
            $target = (integer) $formData['target'];
            var_dump($formData);exit;

        }

        return false;
    }
}
