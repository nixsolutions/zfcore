<?php
/**
 * Menu_Model_Menu_ManagerTest
 *
 * @category    Application
 * @package     Menu_Model_Menu_ManagerTest
 *
 * @author      Alexander Khaylo <alex.khaylo@gmail.com>
 * @copyright   Copyright (c) 2012 NIX Solutions (http://www.nixsolutions.com)
 */
class Menu_Model_Menu_ManagerTest extends ControllerTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->_menuTable = new Menu_Model_Menu_Table();
        $this->_menuManager = new Menu_Model_Menu_Manager();

        $this->_fixture['item'] = array(
            'id'         => 2,
            'label'      => 'Registration2',
            'title'      => 'register',
            'linkType'   => 'mvc',
            'params'     => array("type" => "bot"),
            'parent'     => 0,
            'route'      => 'default',
            'uri'        => NULL,
            'class'      => 'register',
            'target'     => '_self',
            'active'     => 0,
            'visible'    => 1,
            'route_type' => 'module',
            'params' => array(
                'module'     => 'users',
                'controller' => 'register',
                'action'     => 'index'
            )
        );

        $this->_fixture['array'] = array(
            array(
                'label' => null,
                'id' => 0,
                'parentId' => 0,
                'type' => 'mvc',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'index',
                'action' => 'index',
                'uri' => null,
                'class' => 'register',
                'active' => '0',
                'visible' => '0'
            ),
            array(
                'label' => 'Item1',
                'id' => 1,
                'parentId' => 0,
                'type' => 'mvc',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'index',
                'action' => 'index',
                'uri' => null,
                'class' => 'register',
                'active' => '0',
                'visible' => '1'
            ),
            array(
                'label' => 'Item2',
                'title' => 'register',
                'id' => 2,
                'parentId' => 1,
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
                    "action" => "index"
                )
            ),
            array(
                'label' => 'Item3',
                'id' => 3,
                'parentId' => 2,
                'type' => 'mvc',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'index',
                'action' => 'index',
                'uri' => null,
                'class' => 'register',
                'active' => '0',
                'visible' => '1'
            )
        );

    }


    /**
     * make parent child relations
     * and get array item by key
     */
    public function testParentChildRelationsByKey()
    {
        $array = $this->_menuManager->makeParentChildRelations($this->_fixture['array']);
        $this->assertTrue(key($array[1]['pages']) == $this->_fixture['array'][2]['label']);


        $this->assertNull($this->_menuManager->makeParentChildRelations($this->_fixture['array'][0]['label']));
        $this->assertNull(
            $this->_menuManager->getArrayItemByKey(
                $this->_fixture['array'][1],
                'label', $this->_fixture['array'][0]['label']
            )
        );
        $result = $this->_menuManager->getArrayItemByKey($array[1], 'label', $this->_fixture['array'][1]['label']);
        $this->assertTrue(key($result['pages']) == $this->_fixture['array'][2]['label']);
    }


    /**
     * move to by id
     */
    public function testMoveToById()
    {
        $this->assertFalse($this->_menuManager->moveToById(743, 'down'));
        $this->assertTrue($this->_menuManager->moveToById(2, 'down'));
        $this->assertTrue($this->_menuManager->moveToById(2, 'up'));
        $this->assertFalse($this->_menuManager->moveUpItem(1, 1));
        $this->assertFalse($this->_menuManager->moveDownItem(100500, 1));
    }

    /**
     *
     * Get last position by parent
     */
    public function testGetLastPositionByParent()
    {
        $position = $this->_menuManager->getLastPositionByParent($this->_fixture['item']['id']);
        $this->assertTrue($position == 2);
    }

    /**
     * Get row by id
     */
    public function testGetRowById()
    {
        $menuItem = $this->_menuManager->getRowById($this->_fixture['item']['id']);
        $this->assertTrue($menuItem instanceof Core_Db_Table_Row_Abstract);
    }

    /**
     * test getMenuById()
     */
    public function testGetMenuById()
    {
        $this->assertTrue(is_array($this->_menuManager->getMenuById(1)));
        $this->assertNull($this->_menuManager->getMenuById(100500));
    }
    /**
    * test getMenuByLabel()
    */
    public function testGetMenuByLabel()
    {
        $this->assertTrue(is_array($this->_menuManager->getMenuByLabel('Home')));
    }

    /**
     * test checkTarget()
     */
    public function testCheckTarget()
    {
        $this->assertEquals(
            $this->_fixture['item']['target'],
            $this->_menuManager->checkTarget($this->_fixture['item']['target'])
        );
        $this->assertNull($this->_menuManager->checkTarget('someString'));

    }

    /**
     * Get array routes
     */
    public function testGetRoutes()
    {
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
        $menuItem = $this->_menuManager->addMenuItem($this->_fixture['item']);
        $this->assertTrue($menuItem instanceof Core_Db_Table_Row_Abstract);
        $this->assertTrue($this->_fixture['item']['label'] == $menuItem->label);
        //delete
        $this->assertTrue($this->_menuManager->removeById($menuItem->id));
        $this->assertFalse($this->_menuManager->removeById($menuItem->id));
    }

    /**
     * Update
     */
    public function testUpdateMenuItem()
    {
        $this->_fixture['item']['label'] = 'edited';
        $menuItem = $this->_menuManager->updateMenuItem($this->_fixture['item']);
        $this->assertTrue($menuItem instanceof Core_Db_Table_Row_Abstract);
        $this->assertTrue($menuItem->id == $this->_fixture['item']['id']);
        $this->assertTrue($menuItem->label == 'edited');
    }
}
