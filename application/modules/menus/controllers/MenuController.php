<?php
/**
 * @see Core_Controller_Action
 */
require_once 'Core/Controller/Action.php';

/**
 * Menus_MenuController
 *
 * @category    Application
 * @package     Menus_MenuController
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @copyright   Copyright (c) 2010 NIX Solutions (http://www.nixsolutions.com)
 */
class Menus_MenuController extends Core_Controller_Action
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
//        $_menuTable = new Model_Menu_Manager();
//        $_menuArray = $_menuTable->getRawMenuArray();
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
        $_menuTable = new Menus_Model_Menu_Manager();
        $_menuArray = $_menuTable->getRawMenuArray();
        $this->view->assign('items', $_menuArray);
    }

    public function createAction()
    { var_dump(Core_Module_Config::getConfig('acl', null, Core_Module_Config::MAIN_ORDER_FIRST, false));
        $menuTable = new Menus_Model_Menu_Manager();
        $this->menuEditForm = new Menus_Model_Menu_Form_Create();

        if ($this->_request->isPost()
                && $this->menuEditForm->isValid($this->_getAllParams())) {
            try {
                $menuTable->addMenuItem($this->menuEditForm->getValues());
            } catch (Exception $e) {
                return $this->_forward('internal', 'error', 'admin', array('error' => $e->getMessage()));
            }
            $this->_helper->getHelper('redirector')->direct('index');
        }

        $this->view->menuEditForm = $this->menuEditForm;
    }

    public function editAction()
    {
         $id = $this->_getParam('id', 0);
         $this->menuEditForm = new Menus_Model_Menu_Form_Edit();
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        $_menuTable = new Menus_Model_Menu_Manager();
        $isAjax = $this->getRequest()->isXmlHttpRequest();

        if (empty($id) || empty($_menuTable)) {
                if ($isAjax) {
                    $this->_helper->json(0);
                }
            return false;
        }

        $deleted = false;
        if (!empty($id)) {
                $deleted = $_menuTable->removeById($id);
        }

        if ($isAjax) {
            $this->_helper->json($deleted);
        }
        return $deleted;
    }

    public function controllersAction()
    {

        $_menuTable = new Menus_Model_Menu_Manager();
        $_controllers = $_menuTable->getControllersByModuleName($this->_getParam('name', 'default'));

        $this->view->assign('items', $_controllers);
    }

    public function actionsAction()
    {
        $_menuTable = new Menus_Model_Menu_Manager();
        $_actions = $_menuTable->getActionsByControllerName($this->_getParam('name', 'index'));
        $this->view->assign('items', $_actions);
    }

    public function getAllRooutes()
    {


    }
}