<?php
/**
 * Menu_ManagementControllerTest
 *
 * @category    Application
 * @package     ManagementControllerTest
 *
 * @author      Alexander Khaylo <alex.khaylo@gmail.com>
 * @copyright   Copyright (c) 2011 NIX Solutions (http://www.nixsolutions.com)
 */
class Menu_ManagementControllerTest extends ControllerTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->_doLogin(Users_Model_User::ROLE_ADMIN, Users_Model_User::STATUS_ACTIVE);
    }

    public function testIndexAction()
    {
        $this->dispatch('/menus/management/');
        $this->assertQuery('div#gridContainer');
        $this->assertModule('menus');
        $this->assertController('management');
        $this->assertAction('index');
    }

    public function testCreateAction()
    {
        $this->dispatch('/menus/management/create');

        $this->assertModule('menus');
        $this->assertController('management');
        $this->assertAction('create');
        $this->assertQuery('form#menuItemCreateForm');
    }

    public function testEditAction()
    {
        $this->dispatch('menus/management/edit/id/2');

        $this->assertModule('menus');
        $this->assertController('management');
        $this->assertAction('edit');
        $this->assertQuery('form#menuItemEditForm');
    }

    public function testStoreAction()
    {
        $this->dispatch('menus/management/store/?start=0&count=15&sort=-position');

        $this->assertModule('menus');
        $this->assertController('management');
        $this->assertAction('store');

        $response = $this->getResponse()->getBody();
        $this->assertRegExp('/\"controller\"\:\"register\"\,\"action\"\:\"index\"\}\,\{\"id\"\:\"3\"/', $response);
    }

    public function testViewSelectedMenu()
    {
        $this->dispatch('/sitemap.html');
        $this->assertQuery("li.active a[title='sitemap']");
        $this->assertModule('pages');
        $this->assertController('index');
        $this->assertAction('sitemap');
    }

    /**
     * http://zcore.naxel.php.nixsolutions.com/menus/index/move/to/up/id/39
     */
    public function testMoveAction()
    {
        $this->dispatch('/menus/management/move/to/up/id/2');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "true");
        $this->assertModule('menus');
        $this->assertController('management');
        $this->assertAction('move');

        $this->dispatch('/menus/management/move/to/down/id/2');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "true");
        $this->assertModule('menus');
        $this->assertController('management');
        $this->assertAction('move');

        $this->dispatch('/menus/management/move/to/up/id/200');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "false");

        $this->dispatch('/menus/management/move/to/up/id/');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "false");
    }

    public function testDeleteAction()
    {
        $this->dispatch('/menus/management/delete/id/5');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "true");
        $this->assertModule('menus');
        $this->assertController('management');
        $this->assertAction('delete');

        $this->dispatch('/menus/management/delete/id/500');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "false");

        $this->dispatch('/menus/management/delete/id/');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "false");
    }

    public function testGetActionsAction()
    {
        $this->dispatch('/menus/management/get-actions/m/users/c/index');
        $response = $this->getResponse()->getBody();

        $this->assertModule('menus');
        $this->assertController('management');
        $this->assertAction('get-actions');
        $this->assertRegExp('/\"items\"\:\[/', $response);

        $this->dispatch('/menus/management/get-actions/m/');
        $response = $this->getResponse()->getBody();

        $this->assertRegExp('/\"items\"\:false/', $response);
    }

}