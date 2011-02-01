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
     * set up environment
     *
     */
    public function setUp()
    {
        parent::setUp();
    }
    
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
        $this->_doLogin(Model_User::ROLE_ADMIN);
        
        $this->dispatch('/admin/foo');

        $this->assertModule('default');
        $this->assertController('error');
        $this->assertAction('notfound');
    }
    
    /**
     * Admin/Index/Index
     * 
     * allow access for admin
     */
    public function testAdminErrorErrorAction()
    {
        $this->_doLogin(Model_User::ROLE_ADMIN);
        
        $this->dispatch('/admin/error/error');
        
        $this->assertModule('admin');
        $this->assertController('error');
        $this->assertAction('error');
    }
    
    /**
     * Admin/Index/Index
     * 
     * allow access for admin
     */
    public function testAdminErrorNotfoundAction()
    {
        $this->_doLogin(Model_User::ROLE_ADMIN);
        
        $this->dispatch('/admin/error/notfound');
        
        $this->assertModule('admin');
        $this->assertController('error');
        $this->assertAction('notfound');
    }
}