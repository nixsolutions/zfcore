<?php
/**
 * Menu_ManagementController
 *
 * @category    Application
 * @package     ManagementController
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @author      Alexander Khaylo <alex.khaylo@gmail.com>
 * @copyright   Copyright (c) 2012 NIX Solutions (http://www.nixsolutions.com)
 */
class Menu_ManagementController extends Core_Controller_Action_Crud
{
    public function init()
    {
        /* Initialize */
        parent::init();

        $this->_clearAfter();
        $this->_after('_changeViewScriptPathSpec', array('only' => array('index', 'grid')));

        $this->_beforeGridFilter(
            array(
                '_addCheckBoxColumn',
                '_addAllTableColumns',
                '_prepareGrid',
                '_addEditColumn',
                '_addDeleteColumn',
                '_addCreateButton',
                '_addUpButton',
                '_addDownButton',
                '_showFilter'
            )
        );
    }

    /**
     * _getCreateForm
     *
     * return create form for crud
     *
     * @return  Zend_Form
     */
    protected function _getCreateForm()
    {
        return $this;
    }

    /**
     * _getEditForm
     *
     * return edit form for crud
     *
     * @return  Menu_Model_Menu_Form_Create
     */
    protected function _getEditForm()
    {
        return $this;
    }


    /**
     * _getTable
     *
     * return manager for crud
     *
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Menu_Model_Menu_Table();
    }

    /**
     * grid
     *
     * @return void
     */
    public function gridAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->_helper->layout->disableLayout();
        }

        /**
         * todo: do it better way
         * init grid before rendering, catch all exception in action
         */
        $this->grid->getHeaders();
        $this->grid->getData();
        $this->view->grid = $this->grid;
    }

    /**
     * index
     *
     * @return void
     */
    public function indexAction()
    {
        parent::indexAction();
        $this->view->javascript()->action();
    }

    /**
     * create new menu item
     *
     * @return void
     */
    public function createAction()
    {
        $menuManager = new Menu_Model_Menu_Manager();
        $form = new Menu_Model_Menu_Form_Create();

        if ($this->_request->isPost()) {
            if ($this->_request->getParam('linkType') == Menu_Model_Menu::TYPE_URI) {
                $form->uri->setRequired(true);
            }

            if ($form->isValid($this->_getAllParams())) {
                try {
                    $menuManager->addMenuItem($this->_request->getParams());
                } catch (Exception $e) {
                    return $this->_forward('internal', 'error', 'admin', array('error' => $e->getMessage()));
                }
                $this->_helper->getHelper('redirector')->direct('index');
            }
        }

        $routes = $menuManager->getRoutes();
        $this->view->routes = $routes;
        $this->view->form = $form;
        $this->view->javascript()->action();
    }

    /**
     * edit current item
     *
     * @return void
     */
    public function editAction()
    {
        $id = (int)$this->_getParam('id');

        if ($id == 0) {
            $this->_helper->getHelper('redirector')->direct('index');
        }

        $menuManager = new Menu_Model_Menu_Manager();

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

        if ($this->_request->isPost()) {
            if ($this->_request->getParam('linkType') == Menu_Model_Menu::TYPE_URI) {
                $editForm->uri->setRequired(true);
            }

            if ($editForm->isValid($this->_getAllParams())) {
                try {
                    $menuManager->updateMenuItem($this->_request->getParams());
                } catch (Exception $e) {
                    return $this->_forward('internal', 'error', 'admin', array('error' => $e->getMessage()));
                }
                $this->_helper->getHelper('redirector')->direct('index');
            }
        }

        $this->view->menu = $row;
        $this->view->routes = $routes;
        $this->view->form = $editForm;
        $this->view->javascript()->action();
    }

    /**
     * move selected menus UP or DOWN
     *
     * @return void
     */
    public function moveAction()
    {
        $id = (int)$this->_getParam('id');
        $to = $this->_getParam('to');
        $manager = new Menu_Model_Menu_Manager();
        $res = false;
        if ($id && $to) {
            $res = $manager->moveToById($id, $to);
        }
        $this->_helper->json($res);
    }

    /**
     * getActionsAction
     */
    public function getActionsAction()
    {
        $controllerActions = array();
        $controller = $this->_getParam('c');
        $module = $this->_getParam('m');

        if ($controller && $module ) {
            $methods = $this->_getActionsByController($module, $controller);

            if (is_array($methods)) {
                foreach ($methods as $method) {
                    if (preg_match("/^([\w]*)Action$/", $method, $actions)) {
                        $controllerActions[] = strtolower(preg_replace("/([A-Z])/", "-$1", $actions[1]));
                    }
                }
            }
        }
         $this->_helper->json($controllerActions);
    }


     /**
     * get actions by controller enter description here
     *
     * @param string $module
     * @param string $controller
     * @return array an array of method names defined for the class specified by
     * class_name. In case of an error, it returns &null;.
     */
    protected function _getActionsByController($module, $controller)
    {
        $instance = Zend_Controller_Front::getInstance();
        $modules = $instance->getControllerDirectory();
        require_once $modules[$module] . '/' . ucfirst($controller) . 'Controller.php';
        return get_class_methods(ucfirst($module) . '_' . $controller . 'Controller');
    }

    /**
     * add up button
     *
     * @return void
     */
    protected function _addUpButton()
    {
        $link = '<a href="%s" class="button" id="up-button">Up</a>';
        $url = $this->getHelper('url')->url(
            array(
                'action' => 'move',
                'to'     => 'up'
            ),
            'default'
        );
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
        $url = $this->getHelper('url')->url(
            array(
                'action' => 'move',
                'to'     => 'down'
            ),
            'default'
        );
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
            ->removeColumn('action')
            ->setColumn(
                'label',
                array(
                    'formatter' => array(
                        $this->view,
                        array('escape')
                    )
                )
            )->setColumn(
                'uri',
                array(
                    'formatter' => array(
                        $this->view,
                        array('escape')
                    )
                )
            );
    }

}