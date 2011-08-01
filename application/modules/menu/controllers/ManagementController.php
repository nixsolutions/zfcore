<?php
/**
 * Menu_ManagementController
 *
 * @category    Application
 * @package     ManagementController
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @author      Alexander Khaylo <alex.khaylo@gmail.com>
 * @copyright   Copyright (c) 2011 NIX Solutions (http://www.nixsolutions.com)
 */
class Menu_ManagementController extends Core_Controller_Action_Scaffold
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
        return new Menu_Model_Menu_Form_Create();
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
        return new Menu_Model_Menu_Form_Edit();
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
        return new Menu_Model_Menu_Table();
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
        $menuTable = new Menu_Model_Menu_Manager();

        $start  = (int)$this->_getParam('start');
        $count  = (int)$this->_getParam('count');
        $sort   = $this->_getParam('sort', 'path');
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

        $select = $this->_table->select();
        $select->from(
            $this->_table->info(Zend_Db_Table::NAME),
            new Zend_Db_Expr('COUNT(*) as c')
        );

        if ($total = $this->_table->fetchRow($select)) {
            $total = $total->c;
            $select = $this->_table->select();
            $select->from($this->_table->info(Zend_Db_Table::NAME));

            if (isset($order)) {
                $select->order($order);
            }
            $select->limit($count, $start);
            $data = $this->_table->fetchAll($select);
        }

        if ($total) {
            $primary = $this->_table->getPrimary();
            if (is_array($primary)) {
                $primary = current($primary);
            }

            foreach ($data as $val) {
                $array[$val['parentId']][] =  $val;
            }
            $menuTable->buildTree($array, 0, 0, 2);
            //$menuTable->buildTreeGt($array, 0);

            $parentArray = $menuTable->getParentArray();

            $datas = $data->toArray();

            $sortArray = array();
            foreach ($datas as $key => $val) {
                $datas[$key]['label'] = $parentArray[$val['id']];
                $position = 0;
                foreach ($parentArray as $parentKey => $value) {

                    if ($parentKey == $val['id']) {
                        $sortArray[$position] = $datas[$key];
                    }
                    $position++;
                }
            }
            ksort($sortArray);

            $data = new Zend_Dojo_Data($primary, $sortArray);
            $data->setMetadata('numRows', $total);

            $this->_helper->json($data);
        } else {
            $this->_helper->json(false);
        }
    }


    public function createAction()
    {
        $menuManager = new Menu_Model_Menu_Manager();
        $createForm = $this->_getCreateForm();

        if ($this->_request->isPost()
                && $createForm->isValid($this->_getAllParams())) {
            try {
                $menuManager->addMenuItem($this->_request->getParams());
            } catch (Exception $e) {
                return $this->_forward('internal', 'error', 'admin', array('error' => $e->getMessage()));
            }
            $this->_helper->getHelper('redirector')->direct('index');
        }

        $routes = $menuManager->getRoutes();
        $this->view->routes = $routes;
        $this->view->createForm = $createForm;
        $this->view->javascript()->action();
    }

    public function editAction()
    {
        $id = (int)$this->_getParam('id');

        if ($id == 0) {
            $this->_helper->getHelper('redirector')->direct('index');
        }

        $menuManager = new Menu_Model_Menu_Manager();

        $editForm = $this->_getEditForm();
        $editForm = new Menu_Model_Menu_Form_Edit();

        $routes = $menuManager->getRoutes();
        $row = $menuManager->getRowById($id);

        if ($row->type == Menu_Model_Menu::TYPE_MVC) {
            $params = json_decode($row->params);
            if ($params) {
                foreach ($params as $key => $val) {
                    $routes[$row->route]['params'][$key] = $val;
                }
            }
        }

        if ($this->_request->isPost()
                && $editForm->isValid($this->_getAllParams())) {
            try {
                $menuManager->updateMenuItem($this->_request->getParams());
            } catch (Exception $e) {
                return $this->_forward('internal', 'error', 'admin', array('error' => $e->getMessage()));
            }
            $this->_helper->getHelper('redirector')->direct('index');
        }

        $this->view->menu = $row;
        $this->view->routes = $routes;
        $this->view->editForm = $editForm;
        $this->view->javascript()->action();
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $menuTable = new Menu_Model_Menu_Manager();

        $deleted = false;
        if (empty($id) || empty($menuTable)) {
            $this->_helper->json($deleted);
            return false;
        }


        if (!empty($id)) {
            $deleted = $menuTable->removeById($id);
        }
        $this->_helper->json($deleted);

        return $deleted;
    }


    public function moveAction()
    {
        $this->_helper->layout->disableLayout();
        $id = (int)$this->_getParam('id');
        $to = $this->_getParam('to');
        $menuTable = new Menu_Model_Menu_Manager();
        $moved = false;
        if (empty($id) || empty($to) || empty($menuTable)) {
            $this->_helper->json($moved);
            return false;
        }

        if (!empty($id)) {
            $moved = $menuTable->moveToById($id, $to);
        }
        $this->_helper->json($moved);

        return $moved;
    }


    /**
     * get actions by controller
     * Enter description here ...
     * @param string $module
     * @param string $controller
     */
    protected function _getActionsByController($module, $controller)
    {
        $instance = Zend_Controller_Front::getInstance();
        $modules = $instance->getControllerDirectory();
        require_once $modules[$module].'/'.ucfirst($controller).'Controller.php';
        return get_class_methods(ucfirst($module).'_'.$controller.'Controller');
    }

    /**
     * getActionsAction
     */
    public function getActionsAction()
    {
        $module = $this->_getParam('m');
        $controller = $this->_getParam('c');

        $result = false;

        if ($controller && $module) {

            $controllerActions = array();
            $methods = $this->_getActionsByController($module, $controller);

            if (is_array($methods)) {
                foreach ($methods as $method) {
                    if (preg_match("/^([\w]*)Action$/", $method, $actions)) {
                        $action = strtolower(preg_replace("/([A-Z])/", "-$1", $actions[1]));
                        $controllerActions[] = array('name' => $action);
                    }
                }
                $result = $controllerActions;
            }
        }
        $this->view->items = $result;
    }
}