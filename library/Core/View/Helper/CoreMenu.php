<?php

class Core_View_Helper_CoreMenu
    extends Zend_View_Helper_Navigation_Menu
{

    /**
     * label of menu
     */
    private $_menuLabel = null;

    /**
     * id of menu
     */
    private $_menuId = null;

    /*
     * Constructor menu
     */
    public function coreMenu(Zend_Navigation_Container $container = null)
    {
        $menuManager = new Menus_Model_Menu_Manager();

        if ($this->_menuLabel) {
            $menuArray = $menuManager->getMenuByLabel($this->_menuLabel);
            $this->_menuLabel = null;
        } elseif ($this->_menuId) {
            $menuArray = $menuManager->getMenuById($this->_menuId);
            $this->_menuId = null;
        } else {
            $menuArray = $menuManager->getMenuById(0);
        }
         $test1 = array(
             'label' => 'jjjj',
              'id' => 1,
              'type' => 'mvc',
              'route' => 'pages',
             "module" => "pages",
            "controller" => "index",
            "action" => "index",
             'params' => array(
                 "alias" => "about")
         );
  $test2 = array(
  'label' => 'hhh',
  'id' => 2,
  'type' => 'mvc',
  'route' => 'default',
  'module' => 'default',
  'controller' => 'index',
  'action' => 'index',
  'uri' => null,
  'class' => 'register',
  'active' => '0',
  'visible' => '1',
  'pages' =>
    array(
      'register' =>  array(
             'label' => 'register',
             'title' => 'register',
              'id' => 1,
              'type' => 'mvc',
          'route' => 'default',
          'uri' => null,
          'class' => 'register',
          'active' => '1',
          'visible' => '1',
          'route_type' => 'module',
          'module' => 'users',
          'controller' => 'register',
          'action' => 'index',
             'params' => array(
                 "type" => "bot",
                "module" => "users",
                "controller" => "register",
                   "action" => "index",)
         )));

        $container = new Zend_Navigation(array($menuArray));

        $acl = Zend_Registry::get('Acl');
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($identity) {
            $role = $identity->role;
        } else {
            $role = 'guest';
        }
        $this->setAcl($acl)->setRole($role);

        $iterator = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $page) {

            $resourceName  = 'mvc:';

            if (!empty($page->module)) {
                $resourceName .= $page->module . '/';

                 if (!empty($page->controller)) {
                    $resourceName .= $page->controller;
                 } else {
                     $resourceName .= 'index';
                 }

                 if (!empty($page->action)) {
                    $action = $page->action;

                 } else {
                     $action = 'index';
                 }

                 try {

                        if (!$this->getAcl()->isAllowed(
                            $role,
                            $resourceName,
                            $action
                            )){
                                $page->visible = 0;
                            }

                }  catch (Exception $e) {
                           // $page->visible = 0;
                }
            }
        }
        return parent::menu($container);
    }


    /**
     * Set label for menu
     * @param string $label
     */
    public function byLabel($label)
    {
        if (is_string($label)) {
            $this->_menuLabel = $label;
        }
        $this->coreMenu();
        return $this;
    }

    /**
     * Set id for menu
     * @param int $id
     */
    public function byId($id)
    {
        if (is_integer($id)) {
            $this->_menuId = $id;
        }
        $this->coreMenu();
        return $this;
    }
}
