<?php
/**
 * Menu_ManagementControllerTest
 *
 * @category    Application
 * @package     ManagementControllerTest
 *
 * @author      Alexander Khaylo <alex.khaylo@gmail.com>
 * @copyright   Copyright (c) 2012 NIX Solutions (http://www.nixsolutions.com)
 */
class Menu_ManagementControllerTest extends ControllerTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->_doLogin(Users_Model_User::ROLE_ADMIN, Users_Model_User::STATUS_ACTIVE);
    }

    public function testGridAction()
    {
        $this->_request->setMethod("post");
        $this->dispatch('/menu/management/grid');
        $this->assertModule('menu');
        $this->assertController('management');
        $this->assertAction('grid');
    }

    public function testIndexAction()
    {
        $this->dispatch('/menu/management/');
        $this->assertModule('menu');
        $this->assertController('management');
        $this->assertAction('index');
    }

    public function testCreateAction()
    {
        $this->_request->setMethod("post");
        $this->dispatch('/menu/management/create');

        $this->assertModule('menu');
        $this->assertController('management');
        $this->assertAction('create');
        $this->assertQuery('form#menuItemCreateForm');
    }

    public function testEditAction()
    {
        $this->_request->setMethod("post");
        $this->dispatch('menu/management/edit/id/2');

        $this->assertModule('menu');
        $this->assertController('management');
        $this->assertAction('edit');
        $this->assertQuery('form#menuItemCreateForm');
    }

    public function testMoveAction()
    {
        $this->dispatch('menu/management/move/to/down/id/1');
        $this->assertModule('menu');
        $this->assertController('management');
        $this->assertAction('move');

        $this->dispatch('menu/management/move/to/up/id/27');
        $this->assertModule('menu');
        $this->assertController('management');
        $this->assertAction('move');
    }

    public function testViewSelectedMenu()
    {
        $this->dispatch('/sitemap.html');
        $this->assertQuery("li.active a[title='sitemap']");
        $this->assertModule('pages');
        $this->assertController('index');
        $this->assertAction('sitemap');
    }

    public function testGetActionsAction()
    {
        $this->dispatch('/menu/management/get-actions/m/users/c/index');
        $response = $this->getResponse()->getBody();

        $this->assertModule('menu');
        $this->assertController('management');
        $this->assertAction('get-actions');
        $this->assertRegExp('/\[\"index\"\,\"profile\"\]/', $response);

        $this->dispatch('/menu/management/get-actions/m/');
        $response = $this->getResponse()->getBody();
        $this->assertRegExp('/\[\]/', $response);
    }

}