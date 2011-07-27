<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Menus_IndexControllerTest extends ControllerTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->_doLogin(Users_Model_User::ROLE_ADMIN, Users_Model_User::STATUS_ACTIVE);
    }

    public function testIndexAction()
    {
        $this->dispatch('/menus');
        $this->assertQuery('div#gridContainer');
        $this->assertModule('menus');
        $this->assertController('index');
        $this->assertAction('index');
    }

    public function testCreateAction()
    {
        $this->dispatch('/menus/index/create');

        $this->assertModule('menus');
        $this->assertController('index');
        $this->assertAction('create');
        $this->assertQuery('form#menuItemCreateForm');
    }

    public function testEditAction()
    {
        $this->dispatch('menus/index/edit/id/2');

        $this->assertModule('menus');
        $this->assertController('index');
        $this->assertAction('edit');
        $this->assertQuery('form#menuItemEditForm');
    }

    public function testStoreAction()
    {
        $this->dispatch('menus/index/store/?start=0&count=15&sort=-position');

        $this->assertModule('menus');
        $this->assertController('index');
        $this->assertAction('store');

        $response = $this->getResponse()->getBody();
        $this->assertRegExp('/\"items\"\:\[\{\"id\"\:\"2\"\,\"label\"\:\"Registration\"/', $response);
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
        $this->dispatch('/menus/index/move/to/up/id/2');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "true");
        $this->assertModule('menus');
        $this->assertController('index');
        $this->assertAction('move');

        $this->dispatch('/menus/index/move/to/down/id/2');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "true");
        $this->assertModule('menus');
        $this->assertController('index');
        $this->assertAction('move');

        $this->dispatch('/menus/index/move/to/up/id/200');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "false");

        $this->dispatch('/menus/index/move/to/up/id/');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "false");
    }

    public function testDeleteAction()
    {
        $this->dispatch('/menus/index/delete/id/5');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "true");
        $this->assertModule('menus');
        $this->assertController('index');
        $this->assertAction('delete');

        $this->dispatch('/menus/index/delete/id/500');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "false");

        $this->dispatch('/menus/index/delete/id/');
        $response = $this->getResponse()->getBody();
        $this->assertTrue($response == "false");
    }

    public function testGetActionsAction()
    {
        $this->dispatch('/menus/index/get-actions/m/users/c/index');
        $response = $this->getResponse()->getBody();

        $this->assertModule('menus');
        $this->assertController('index');
        $this->assertAction('get-actions');
        $this->assertRegExp('/\"items\"\:\[/', $response);

        $this->dispatch('/menus/index/get-actions/m/');
        $response = $this->getResponse()->getBody();

        $this->assertRegExp('/\"items\"\:false/', $response);
    }

}