<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Users_IndexControllerTest extends ControllerTestCase
{

    /**
     * Admin/Index/Index
     *
     * denied access for guests
     */
    public function testGuestIndexAction()
    {
        $this->_doLogin(Users_Model_User::ROLE_GUEST);
        $this->dispatch('/users/');
        $this->assertModule('users');
        $this->assertController('error');
        $this->assertAction('denied');
    }

    /**
     * Admin/Index/Index
     *
     * allow access for admin
     */
    public function testUserIndexAction()
    {
        $this->_doLogin(Users_Model_User::ROLE_USER);

        $this->dispatch('/users/');

        $this->assertModule('users');
        $this->assertController('index');
        $this->assertAction('index');
    }
}