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
        $this->assertModule('menus');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertQuery('div#gridContainer');
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
        $this->dispatch('menus/index/edit/id/1');
        $this->assertModule('menus');
        $this->assertController('index');
        $this->assertAction('edit');
        $this->assertQuery('form#menuItemEditForm');
    }

    public function testViewSelectedMenu()
    {
        $this->dispatch('/sitemap.html');
        $this->assertModule('pages');
        $this->assertController('index');
        $this->assertAction('sitemap');
        $this->assertQuery("li.active a[title='sitemap']");
    }




}