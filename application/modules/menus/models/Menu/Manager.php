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
     * Array for creating parent's menu in select
     */
    protected $_parentArray = array();

    /**
     * Default root directory with id = 0
     */
    protected $_rootDirectory = array(
        'label'   => null,
        'id'      => 0,
        'type'    => 'uri',
        'uri'   => '/',
        'visible' => true,
        'active' => false
    );
    /**
     * Menu tree
     *
     * @var null|Menus_Model_Menu
     */
    protected $_menuTableRowset = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDbTable(new Menus_Model_Menu_Table());
        $this->setMenuRowset();
    }

    /**
     * Get parent menu option for form selectbox
     *
     * @return mixed
     */
    public function getMenuItemsForEditForm() {

        $this->setMenuRowset();
         $cats = array();
        foreach ($this->_menuTableRowset as $data) {
            $array[$data['parent_id']][] =  $data;
        }
        $this->buildTree($array, 0, 0, 3);
        return $this->_parentArray;
    }


    /**
     * Build hierarchical tree for menu of parents
     *
     * @var $array array
     * @var $parent_id int
     * @var $level int
     * @var $shift int
     */
    public function buildTree($array, $parent_id, $level, $shift)
    {
        if ($parent_id == 0) {
            $this->_parentArray = array(0 => 'No parent');
        }
        if (is_array($array) && isset($array[$parent_id]) && count($array[$parent_id]) > 0) {
            foreach ($array[$parent_id] as $cat) {
                $level = $level + $shift;
                $this->_parentArray[$cat['id']] = str_pad('', $level - $shift, "-", STR_PAD_RIGHT).$cat['label'];
                $this->buildTree($array, $cat['id'], $level, $shift);
                $level = $level - $shift;
            }
        }
    }

    public function getTargetOptionsForEditForm() {
        return $targetMenus = array(
            0 => 'Don\'t set',
            Menus_Model_Menu::TARGET_BLANK  => "New window",
            Menus_Model_Menu::TARGET_PARENT => "Parrent frame",
            Menus_Model_Menu::TARGET_SELF   => "Current window",
            Menus_Model_Menu::TARGET_TOP    => "New window without frames"
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
        $arr = $this->getArrayItemByKey($menuArray, 'id', $id);
        return $arr;
    }

    public function getMenuByLabel($label = null)
    {
        $menuArray = $this->getMenuArray();
        $arr = $this->getArrayItemByKey($menuArray, 'label', $label);
        return $arr;
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
                    $arr['label'] = null;
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

            if (isset($entry['params']) && is_string($entry['params'])) {
                $entry['params'] = (array)json_decode($entry['params']);
            }
            if ($entry['parent_id'] == 0) {
                $all[$id] = $entry;
                $outputArray[] = &$all[$id];

            } else {
                $dangling[$id] = $entry;
            }
        }
        $i = 0;
        while (count($dangling) > 0) {
            foreach($dangling as $entry) {
                $id = $entry['id'];
                $pid = $entry['parent_id'];

                if (isset($all[$pid])) {
                    $all[$id] = $entry;
                    $all[$pid]['pages'][$entry['label']] =& $all[$id];

                    unset($all[$id]['parent_id']);
                    unset($dangling[$entry['id']]);
                }
            }
            $i++;
            if ($i > 50) {
                var_dump($dangling);
                exit('Error in while() '.count($dangling));
                break;
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
        $menuArray = $this->_rootDirectory;
        $menuArray['pages'] = $this->_menuArray;
        return $menuArray;
    }

    /**
     * Return _menuArray->toArray()
     *
     * @return array
     */
    public function getRawMenuArray()
    {
        $result = $this->getDbTable()->getMenuItems();
        return $result->toArray();
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
        //update child if deleted paren
        $where = $this->getDbTable()->getAdapter()->quoteInto('parent_id = ?', $id);
        $this->getDbTable()->update(array('parent_id'  => 0), $where);
        return true;
    }

    /**
     *
     * getRoutes
     * @param $onlyNames bool
     * @return array
     */
    public function getRoutes($onlyNames = false)
    {

        $instance = Zend_Controller_Front::getInstance();
        $routes = $instance->getRouter()->getRoutes();
        $num = 0;
        $numes = array();
        foreach ($routes as $routeName => $route) {
            if ($onlyNames) {
                $numes[$num] = $routeName;
                $num++;
            } else {
                if ($routes[$routeName] instanceof Zend_Controller_Router_Route_Static) {
                    $routesArray[$routeName] = $this->getInfoFromStaticRoute($routeName, (array)$route);
                } elseif ($routes[$routeName] instanceof Zend_Controller_Router_Route) {
                    $routesArray[$routeName] = $this->getInfoFromRoute($routeName, (array)$route);
                } elseif ($routes[$routeName] instanceof Zend_Controller_Router_Route_Regex) {
                    $routesArray[$routeName] = $this->getInfoFromRegexRoute($routeName, (array)$route);

                } elseif ($routes[$routeName] instanceof Zend_Controller_Router_Route_Module) {
                    $routesArray[$routeName] = $this->getInfoFromModuleRoute($routeName, (array)$route, $instance);
                } else {
                    var_dump($routes[$routeName]);
                    $num--;
                }
                $num++;
            }

        }
        if ($onlyNames) {
            return $numes;
        }
        return $routesArray;
    }
    /**
     *
     * getInfoFromStaticRoute
     * @param $routeName string
     * @param $route array
     * @return array
     */
    public function getInfoFromStaticRoute($routeName, $route)
    {
        foreach ($route as $names => $val) {
            if (preg_match("/_route/i", $names)) {
                $path = $val;
            }
            if (preg_match("/_defaults/i", $names)) {
                $defaults = $val;
            }
        }
        return array(
            'name'       => $routeName,
            'path'       => '/' . $path,
               'type'       => Menus_Model_Menu::ROUTE_TYPE_STATIC,
            'module'     => $defaults['module'],
              'controller' => $defaults['controller'],
              'action'     => $defaults['action']
        );
    }

    /**
     *
     * getInfoFromRoute
     * @param $routeName string
     * @param $route array
     * @return array
     */
    public function getInfoFromRoute($routeName, $route)
    {
        $params = array();
        $parts = array();
        $path = '';
        $wildcard = false;
        foreach ($route as $names => $val) {
            if (preg_match("/_variables/i", $names)) {
                foreach ($val as $names => $param) {
                    $params[$names] = $param;
                }
            }
            if (preg_match("/_part/i", $names)) {
                foreach ($val as $names => $part) {
                    $parts[$names] = $part;
                    if ($part == "*") {
                        $wildcard = true;
                    }
                }
            }
            if (preg_match("/_defaults/i", $names)) {
                $defaults = $val;
            }
        }

        foreach ($parts as $names => $part) {
            if (isset($params[$names])) {
                $path .= $part . "/:" . $params[$names];
            } else {
                $path .= "/" . $part;
            }
        }
        return array(
            'name'     => $routeName,
            'path'     => $path,
               'type'     => Menus_Model_Menu::ROUTE_TYPE_ROUTE,
            'wildcard' => $wildcard,
            'params'   => $params,
            'module'     => $defaults['module'],
              'controller' => $defaults['controller'],
              'action'     => $defaults['action']
        );
    }

    /**
     *
     * getInfoFromRegexRoute
     * @param $routeName string
     * @param $route array
     * @return array
     */
    public function getInfoFromRegexRoute($routeName, $route)
    {
        $params = array();
        $path = '';
        foreach ($route as $names => $val) {

            if (preg_match("/_regex/i", $names)) {
                $regex = $val;
            }
            if (preg_match("/_map/i", $names)) {
                $num = 0;
                foreach ($val as $param => $val) {
                    $params[$num] = $param;
                    $num++;
                }
            }
            if (preg_match("/_defaults/i", $names)) {
                $defaults = $val;
            }
        }
        $pattern = "/\([\S\s]*\)/i";
        for ($i = 0; $i < $num; $i++) {
            $regex =  preg_replace($pattern, $params[$i], $regex, 1);
        }
        $path = str_replace("\\", "", $regex);

        return array(
            'name'   => $routeName,
            'path'   => '/' . $path,
               'type'   => Menus_Model_Menu::ROUTE_TYPE_REGEX,
            'params' => $params,
            'module'     => $defaults['module'],
              'controller' => $defaults['controller'],
              'action'     => $defaults['action']
        );
    }

    /**
     *
     * getInfoFromModuleRoute
     * @param $routeName string
     * @param $values array
     * @param Zend_Controller_Front::getInstance()
     * @return array
     */
    public function getInfoFromModuleRoute($routeName, $route, $instance)
    {
        $modules = $instance->getControllerDirectory();

        return array(
            'name'   => $routeName,
            'path'   => '/:module/:controller/:action',
               'type'   => Menus_Model_Menu::ROUTE_TYPE_MODULE,
            'modules' => $modules
        );
    }



    public function addMenuItem($data = null)
    {
        if (!empty($data) && is_array($data)) {

            $label = $data['label'];
            $parent = (integer) $data['parent'];
            $target = (integer) $data['target'];

            $menu = $this->getDbTable()->create();

            $options = array();

            if (isset($data['params']) && is_array($data['params'])) {
                $options = $data['params'];
            }
            $menu->visible = (int)$data['visible'];
            $menu->active = (int)$data['active'];
            if ($data['class']) {
                $menu->class = $data['class'];
            }
            if ($data['target'] == Menus_Model_Menu::TARGET_BLANK ||
                $data['target'] == Menus_Model_Menu::TARGET_PARENT ||
                $data['target'] == Menus_Model_Menu::TARGET_SELF ||
                $data['target'] == Menus_Model_Menu::TARGET_TOP) {

                $menu->target = $data['target'];
            }

            if ($data['linkType'] == 0) {//link
                $menu->type = Menus_Model_Menu::TYPE_URI;
                if (!$data['uri']) {
                    return false;
                }
                $menu->uri = $data['uri'];


            } elseif ($data['linkType'] == 1) {//route
                $menu->type = Menus_Model_Menu::TYPE_MVC;
                if (!$data['route']) {
                    return false;
                }
                $routes = $this->getRoutes();
                if (!isset($routes[$data['route']])) {
                    return false;
                }


                $menu->route_type = $routes[$data['route']]['type'];

                if($menu->route_type != Menus_Model_Menu::ROUTE_TYPE_MODULE){
                    $menu->module     = $routes[$data['route']]['module'];
                    $menu->controller = $routes[$data['route']]['controller'];
                    $menu->action     = $routes[$data['route']]['action'];
                } else {
                    $menu->module     = $options['module'];
                    $menu->controller = $options['controller'];
                    $menu->action     = $options['action'];
                }

                $menu->route = $routes[$data['route']]['name'];
            }

            $menu->label     = $data['label'];
            $menu->title     = $data['title'];
            $menu->params    = json_encode($options);
            $menu->parent_id = $data['parent'];
            //$menu->order   = $data['order'];

        if ( $menu->save() ) {
            return $menu;
        }

        return false;

        }

        return false;
    }
}
