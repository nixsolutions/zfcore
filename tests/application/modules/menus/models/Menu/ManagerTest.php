<?php
class Menus_Model_Menu_ManagerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->_menuTable = new Menus_Model_Menu_Table();
        $this->_menuManager = new Menus_Model_Menu_Manager(Zend_Controller_Front::getInstance());

        $this->_fixture = array(
            'id'         => 2,
            'label'      => 'Registration2',
            'title'      => 'register',
            'linkType'   => 1,
            'params'     => array("type" => "bot"),
            'parent'  => 0,
            //'position' => 1,
            'route'      => 'default',
            'uri'        => NULL,
            'class'      => 'register',
            'target'     => '_parent',
            'active'     => 0,
            'visible'    => 1,
            'route_type' => 'module',
            'params' => array(
                'module'     => 'users',
                'controller' => 'register',
                'action'     => 'index'
            )
        );

    }


    public function testMoveToById()
    {
        $this->assertFalse($this->_menuManager->moveToById($this->_fixture['id'], 'down'));
        $this->assertTrue($this->_menuManager->moveToById($this->_fixture['id'], 'up'));
    }

    /**
     *
     * Get last position by parent
     */
    public function testGetLastPositionByParent()
    {
        $position = $this->_menuManager->getLastPositionByParent($this->_fixture['id']);
        $this->assertTrue($position == 2);
    }
    /**
     * Get row by id
     */
    public function testGetRowById()
    {
        $this->dispatch('/');
        $menuItem = $this->_menuManager->getRowById($this->_fixture['id']);
        $this->assertTrue($menuItem instanceof Core_Db_Table_Row_Abstract);
    }

    /**
     * Get array routes
     */
    public function testGetRoutes()
    {
        $this->dispatch('/');
        $menuItem = $this->_menuManager->getRoutes();
        $this->assertArrayHasKey('default', $menuItem);
        $this->assertArrayHasKey('login', $menuItem);
        $this->assertArrayHasKey('logout', $menuItem);
    }

    /**
     * Get array names of routes
     */
    public function testGetNamesOfRoutes()
    {
        $this->dispatch('/');
        $menuItem = $this->_menuManager->getNamesOfRoutes();
        $this->assertArrayHasKey('default', $menuItem);
        $this->assertArrayHasKey('login', $menuItem);
        $this->assertArrayHasKey('logout', $menuItem);
    }


    /**
     * Create and Delete
     */
    public function testAddAndRemoveMenuItem()
    {
        //create
        $this->dispatch('/');
        $menuItem = $this->_menuManager->addMenuItem($this->_fixture);
        $this->assertTrue($menuItem instanceof Core_Db_Table_Row_Abstract);
        $this->assertTrue($this->_fixture['label'] == $menuItem->label);
        //delete
        $this->assertTrue($this->_menuManager->removeById($menuItem->id));
        $this->assertFalse($this->_menuManager->removeById($menuItem->id));
    }

    /**
     * Update
     */
    public function testUpdateMenuItem()
    {
        $this->dispatch('/');
        $this->_fixture['label'] = 'edited';
        $menuItem = $this->_menuManager->updateMenuItem($this->_fixture);
        $this->assertTrue($menuItem instanceof Core_Db_Table_Row_Abstract);
        $this->assertTrue($menuItem->id == $this->_fixture['id']);
        $this->assertTrue($menuItem->label == 'edited');
    }

}
