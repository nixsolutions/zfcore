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
class Menus_IndexController extends Core_Controller_Action
{
    public function init()
    {
        /* Initialize */
        parent::init();

        /* is Dashboard Controller */
        $this->_isDashboard();
        $this->_helper->contextSwitch()
                ->addActionContext('store', 'json')
                ->addActionContext('controllers', 'json')
                ->addActionContext('actions', 'json')
                ->initContext('json');
    }

    /**
     * indexAction
     *
     * Index action in Menu Controller
     *
     * @access public
     */
    public function indexAction()
    {
        //$_menuTable = new Model_Menu_Manager();
        //$_menuArray = $_menuTable->getRawMenuArray();
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
        $menuArray = $menuTable->getRawMenuArray();
        $this->view->items = $menuArray;
    }


    public function createAction()
    {
        $menuTable = new Menus_Model_Menu_Manager();
        $menuEditForm = new Menus_Model_Menu_Form_Create();

        if ($this->_request->isPost()
                && $menuEditForm->isValid($this->_getAllParams())) {
            try {
                $menuTable->addMenuItem($this->_request->getParams());
            } catch (Exception $e) {
                return $this->_forward('internal', 'error', 'admin', array('error' => $e->getMessage()));
            }
            $this->_helper->getHelper('redirector')->direct('index');
        }

        $routes = $menuTable->getRoutes();
        $this->view->routes = $routes;
        $this->view->menuEditForm = $menuEditForm;
        $this->view
                 ->headScript()
                 ->appendFile($this->view->baseUrl('scripts/jquery/jquery.js?ver=1.4.2'));
        $this->view
                 ->javascript()
                 ->action();
    }

    public function editAction()
    {
         $id = $this->_getParam('id', 0);
         $menuEditForm = new Menus_Model_Menu_Form_Edit();
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

    public function controllersAction()
    {

        /*$_menuTable = new Menus_Model_Menu_Manager();
        $_controllers = $_menuTable->getControllersByModuleName($this->_getParam('name', 'default'));
        $this->view->assign('items', $_controllers);*/
    }

    public function actionsAction()
    {
        /*$_menuTable = new Menus_Model_Menu_Manager();
        $_actions = $_menuTable->getActionsByControllerName($this->_getParam('name', 'index'));
        $this->view->assign('items', $_actions);*/
    }
}