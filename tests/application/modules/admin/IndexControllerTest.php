<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Admin
 */
class Admin_IndexControllerTest extends ControllerTestCase
{
    /**
     * Admin/Index/Index
     *
     * allow access for admin
     */
    public function testAdminIndexAction()
    {
        $this->_doLogin(Users_Model_User::ROLE_ADMIN);

        $this->dispatch('/admin/');
        $this->assertModule('admin');
        $this->assertController('index');
        $this->assertAction('index');
    }
}