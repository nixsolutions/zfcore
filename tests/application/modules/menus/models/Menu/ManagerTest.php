<?php
class Menus_Model_Menu_ManagerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->_menuTable = new Menus_Model_Menu_Table();
        $this->_menuManager = new Menus_Model_Menu_Manager();

        $this->_fixture['item'] = array(
            'id'         => 2,
            'label'      => 'Registration2',
            'title'      => 'register',
            'linkType'   => 1,
            'params'     => array("type" => "bot"),
            'parent'  => 0,
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

        $this->_fixture['array'] = array(
            array(
                'label' => 'Item1',
                'id' => 1,
                'parent_id' => 0,
                'type' => 'mvc',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'index',
                'action' => 'index',
                'uri' => null,
                'class' => 'register',
                'active' => '0',
                'visible' => '1'),
            array(
                 'label' => 'Item2',
                 'title' => 'register',
                  'id' => 2,
                'parent_id' => 1,
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
                   'parent_id' => 2,
                'type' => 'mvc',
                'route' => 'default',
                'module' => 'default',
                'controller' => 'index',
                'action' => 'index',
                'uri' => null,
                'class' => 'register',
                'active' => '0',
                'visible' => '1')
        );

    }


    /**
     * make parent child relations
     * and get array item by key
     */
    public function testParentChildRelationsByKey()
    {
        $array = $this->_menuManager->makeParentChildRelations($this->_fixture['array']);
        $this->assertTrue(key($array[0]['pages']) == $this->_fixture['array'][1]['label']);

        $this->assertNull($this->_menuManager->makeParentChildRelations($this->_fixture['array'][0]['label']));
        $this->assertNull(
            $this->_menuManager->getArrayItemByKey(
                $this->_fixture['array'][0]['label'],
                'label', $this->_fixture['array'][0]['label']
            )
        );
        $result = $this->_menuManager->getArrayItemByKey($array, 'label', $this->_fixture['array'][0]['label']);
        $this->assertTrue(key($result['pages']) == $this->_fixture['array'][1]['label']);
    }


    /**
     * move to by id
     */
    public function testMoveToById()
    {
        $this->assertFalse($this->_menuManager->moveToById(743, 'down'));
        $this->assertFalse($this->_menuManager->moveToById(2, 'down'));
        $this->assertTrue($this->_menuManager->moveToById(2, 'up'));
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
        $this->dispatch('/');
        $menuItem = $this->_menuManager->getRowById($this->_fixture['item']['id']);
        $this->assertTrue($menuItem instanceof Core_Db_Table_Row_Abstract);
    }



    public function testGetTypeOptionsForEditForm()
    {
        $array = $this->_menuManager->getTypeOptionsForEditForm();
        $this->assertArrayHasKey('uri', $array);
        $this->assertArrayHasKey('mvc', $array);
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
        $this->dispatch('/');
        $this->_fixture['item']['label'] = 'edited';
        $menuItem = $this->_menuManager->updateMenuItem($this->_fixture['item']);
        $this->assertTrue($menuItem instanceof Core_Db_Table_Row_Abstract);
        $this->assertTrue($menuItem->id == $this->_fixture['item']['id']);
        $this->assertTrue($menuItem->label == 'edited');
    }

}
