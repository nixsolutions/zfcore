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
class Menu_ManagementController extends Core_Controller_Action_Crud
{
    public function init()
    {
        /* Initialize */
        parent::init();

        $this->_beforeGridFilter(array(
             '_addCheckBoxColumn',
             '_addAllTableColumns',
             '_prepareGrid',
             '_addEditColumn',
             '_addDeleteColumn',
             '_addCreateButton',
             '_addUpButton',
             '_addDownButton',
             '_showFilter'
        ));

        $this->_after('_setDefaultScriptPath', array('only' => array('create', 'edit')));
    }

    /**
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Form
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
     * @return  Zend_Form
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
        $this->view->headScript()->appendFile(
            $this->view->baseUrl('./modules/menu/scripts/management/index.js'
        ));
        parent::indexAction();
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
     * add up button
     *
     * @return void
     */
    protected function _addUpButton()
    {
        $link = '<a href="%s" class="button" id="up-button">Up</a>';
        $url = $this->getHelper('url')->url(array(
            'action' => 'move',
            'to' => 'up'
        ), 'default');
        $this->view->placeholder('grid_buttons')->create .= sprintf($link, $url);
    }

    /**
     * add down button
     *
     * @return void
     */
    protected function _addDownButton()
    {
        $link = '<a href="%s" class="button" id="down-button">Down</a>';
        $url = $this->getHelper('url')->url(array(
            'action' => 'move',
            'to' => 'down'
        ), 'default');
        $this->view->placeholder('grid_buttons')->create .= sprintf($link, $url);
    }

    /**
     * remove needless rows
     *
     * @return void
     */
    protected  function _prepareGrid()
    {
        $this->grid
                ->removeColumn('title')
                ->removeColumn('class')
                ->removeColumn('target')
                ->removeColumn('active')
                ->removeColumn('params')
                ->removeColumn('visible')
                ->removeColumn('routeType')
                ->removeColumn('module')
                ->removeColumn('controller')
                ->removeColumn('action');
    }

}