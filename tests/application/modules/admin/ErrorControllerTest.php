<?php
/**
 * ErrorControllerTest
 *
 * @category Tests
 * @package  Admin
 */
class Admin_ErrorControllerTest extends ControllerTestCase
{
    /**
     * Admin/Index/Index
     *
     * denied access for guests
     */
    public function testGuestAccessDenied()
    {
        $this->dispatch('/admin');

        $this->assertModule('users');
        $this->assertController('login');
        $this->assertAction('index');
    }

    /**
     * Admin/Index/Index
     *
     * allow access for admin
     */
    public function testAdminInvalidController()
    {
        $this->_doLogin(Users_Model_User::ROLE_ADMIN);

        $this->dispatch('/admin/foo');

        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('notfound');
    }

    /**
     * Admin/Index/Index
     *
     * allow access for admin
     */
    public function testAdminInvalidAction()
    {
        $this->_doLogin(Users_Model_User::ROLE_ADMIN);

        $this->dispatch('/admin/index/foo');

        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
    }
}