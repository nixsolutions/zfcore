<?php
/**
 * @see Core_Controller_Action
 */
require_once 'Core/Controller/Action.php';

/**
 * Menus_IndexController
 *
 * @category    Application
 * @package     Menus_IndexController
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @copyright   Copyright (c) 2010 NIX Solutions (http://www.nixsolutions.com)
 */
class Menus_IndexController extends Core_Controller_Action_Scaffold
{
    public function init()
    {
        /* Initialize */
        parent::init();

        /* is Dashboard Controller */
        $this->_isDashboard();

        $this->_helper->contextSwitch()
                ->addActionContext('get-actions', 'json')
                ->initContext('json');
    }

    /**
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    protected function _getCreateForm()
    {

    }

    /**
     * _getEditForm
     *
     * return edit form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    protected function _getEditForm()
    {

    }

    /**
     * _getTable
     *
     * return manager for scaffolding
     *
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Menus_Model_Menu_Table();
    }

    /**
     * indexAction
     *
     * Index action in Index Controller
     *
     * @access public
     */
    public function indexAction()
    {
        //...
    }

    /**
     * storeAction
     *
     * Get store action
     *
     * @access public
     */
    public function storeAction()
    {
        $menuTable = new Menus_Model_Menu_Manager();

        $start  = $this->_getParam('start');
        $count  = $this->_getParam('count');
        $sort   = $this->_getParam('sort', 'path');
        $field  = $this->_getParam('field');
        $filter = $this->_getParam('filter');

        // sort data
        //   field  - ASC
        //   -field - DESC
        if ($sort && ltrim($sort, '-')
            && in_array(ltrim($sort, '-'), $this->_table->info(Zend_Db_Table::COLS))
        ) {
            if (strpos($sort, '-') === 0) {
                $order = ltrim($sort, '-') .' '. Zend_Db_Select::SQL_DESC;
            } else {
                $order = $sort  .' '.  Zend_Db_Select::SQL_ASC;
            }
        }

        // Use LIKE for filter
        if ($field && in_array($field, $this->_table->info(Zend_Db_Table::COLS))
            && $filter && $filter != '*') {

            $filter = str_replace('*', '%', $filter);
            $filter = $this->_table->getAdapter()->quote($filter);

            $where = $field .' LIKE '. $filter;
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        switch ($db) {
            case $db instanceof Zend_Db_Adapter_Pdo_Mysql :
                $select = $this->_table->select();
                $select->from(
                    $this->_table->info(Zend_Db_Table::NAME),
                    new Zend_Db_Expr('SQL_CALC_FOUND_ROWS *')
                );
                if (isset($where)) {
                    $select->where($where);
                }
                if (isset($order)) {
                    $select->order($order);
                }
                $select->limit($count, $start);
                if ($data = $this->_table->fetchAll($select)) {
                    $total = $this->_table->getAdapter()
                    ->fetchOne(
                        $this->_table->getAdapter()->select()->from(null, new Zend_Db_Expr('FOUND_ROWS()'))
                    );
                }
                break;
            default:
                $select = $this->_table->select();
                $select->from(
                    $this->_table->info(Zend_Db_Table::NAME),
                    new Zend_Db_Expr('COUNT(*) as c')
                );
                if (isset($where)) {
                    $select->where($where);
                }
                if ($total = $this->_table->fetchRow($select)) {
                    $total = $total->c;
                    $select = $this->_table->select();
                    $select->from($this->_table->info(Zend_Db_Table::NAME));
                    if (isset($where)) {
                        $select->where($where);
                    }
                    if (isset($order)) {
                        $select->order($order);
                    }
                    $select->limit($count, $start);
                    $data = $this->_table->fetchAll($select);
                }
                break;
        }

        if ($total) {
            $primary = $this->_table->getPrimary();
            if (is_array($primary)) {
                $primary = current($primary);
            }

            foreach ($data as $val) {
                $array[$val['parent_id']][] =  $val;
            }
            $menuTable->buildTreeGt($array, 0);
            $parentArray = $menuTable->getParentArray();

            $datas = $data->toArray();

            foreach ($data as $key => $val) {

                $datas[$key]['parent'] = $parentArray[$val['id']];
            }

            $data = new Zend_Dojo_Data($primary, $datas);
            $data->setMetadata('numRows', $total);

            $this->_helper->json($data);
        } else {
            $this->_helper->json(false);
        }
    }


    public function createAction()
    {
        $menuManager = new Menus_Model_Menu_Manager();
        $menuCreateForm = new Menus_Model_Menu_Form_Create();

        if ($this->_request->isPost()
                && $menuCreateForm->isValid($this->_getAllParams())) {
            try {
                $menuManager->addMenuItem($this->_request->getParams());
            } catch (Exception $e) {
                return $this->_forward('internal', 'error', 'admin', array('error' => $e->getMessage()));
            }
            $this->_helper->getHelper('redirector')->direct('index');
        }

        $routes = $menuManager->getRoutes();
        $this->view->routes = $routes;
        $this->view->menuCreateForm = $menuCreateForm;
        $this->view->javascript()->action();
    }

    public function editAction()
    {
        $id = (int)$this->_getParam('id');

        if ($id == 0) {
            $this->_helper->getHelper('redirector')->direct('index');
        }

        $menuManager = new Menus_Model_Menu_Manager();
        $menuEditForm = new Menus_Model_Menu_Form_Edit();

        $routes = $menuManager->getRoutes();
        $row = $menuManager->getRowById($id);

        if ($row->type == Menus_Model_Menu::TYPE_MVC) {
            $params = json_decode($row->params);
            if ($params) {
                foreach ($params as $key => $val) {
                    $routes[$row->route]['params'][$key] = $val;
                }
            }
        }

        if ($this->_request->isPost()
                && $menuEditForm->isValid($this->_getAllParams())) {
            try {
                $menuManager->updateMenuItem($this->_request->getParams());
            } catch (Exception $e) {
                return $this->_forward('internal', 'error', 'admin', array('error' => $e->getMessage()));
            }
            $this->_helper->getHelper('redirector')->direct('index');
        }

        $this->view->menu = $row;
        $this->view->routes = $routes;
        $this->view->menuEditForm = $menuEditForm;
        $this->view->javascript()->action();
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        $menuTable = new Menus_Model_Menu_Manager();
        $isAjax = $this->getRequest()->isXmlHttpRequest();

        if (empty($id) || empty($menuTable)) {
                if ($isAjax) {
                    $this->_helper->json(0);
                }
            return false;
        }

        $deleted = false;
        if (!empty($id)) {
            $deleted = $menuTable->removeById($id);
        }

        if ($isAjax) {
            $this->_helper->json($deleted);
        }
        return $deleted;
    }


    public function moveAction()
    {
        $id = (int)$this->_getParam('id', 0);
        $to = $this->_getParam('to');
        $menuTable = new Menus_Model_Menu_Manager();
        $isAjax = $this->getRequest()->isXmlHttpRequest();

        if (empty($id) || empty($to) || empty($menuTable)) {
            if ($isAjax) {
                $this->_helper->json(0);
            }
            return false;
        }
        $moved = false;
        if (!empty($id)) {
            $moved = $menuTable->moveToById($id, $to);
        }
        if ($isAjax) {
            $this->_helper->json($moved);
        }
        return $moved;
    }


    public function getActionsAction()
    {
        $module = $this->_getParam('m');
        $controller = $this->_getParam('c');
        $controllerActions = array();

        $instance = Zend_Controller_Front::getInstance();
        $modules = $instance->getControllerDirectory();
        require_once $modules[$module].'/'.ucfirst($controller).'Controller.php';
        $methods = get_class_methods(ucfirst($module).'_'.$controller.'Controller');
        if (is_array($methods)) {
            foreach ($methods as $method) {
                if (preg_match("/^([\w]*)Action$/", $method, $actions)) {
                    $action = strtolower(preg_replace("/([A-Z])/", "-$1", $actions[1]));
                    $controllerActions[] = array('name' => $action);
                }
            }
        }
        $this->view->items = $controllerActions;
    }
}