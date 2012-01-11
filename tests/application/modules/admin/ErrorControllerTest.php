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
    public function testAdminInvalidAction()
    {
        $this->_doLogin(Users_Model_User::ROLE_ADMIN);

        $this->dispatch('/admin/index/foo');

        $this->assertModule('users');
        $this->assertController('error');
        $this->assertAction('error');
    }

    /**
     * Admin/Index/Index
     *
     * allow access for admin
     * FIXME:
     */
    /*public function testAdminErrorErrorAction()
    {
        $this->_doLogin(Users_Model_User::ROLE_ADMIN);

        $this->dispatch('/admin/error/error');

        $this->assertModule('admin');
        $this->assertController('error');
        $this->assertAction('error');
    }*/

    /**
     * Admin/Index/Index
     *
     * allow access for admin
     */
    public function testAdminErrorNotfoundAction()
    {
        $this->_doLogin(Users_Model_User::ROLE_ADMIN);

        $this->dispatch('/admin/error/notfound');

        $this->assertModule('admin');
        $this->assertController('error');
        $this->assertAction('notfound');
    }
}