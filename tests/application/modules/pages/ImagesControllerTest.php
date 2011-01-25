<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Pages_ImagesControllerTest extends ControllerTestCase
{
    /**
     * Setup TestCase
     */
    public function setUp()
    {
        parent::setUp();        
        parent::_doLogin(Model_User::ROLE_ADMIN);
    }
    
    /**
     * Admin/Images/Index
     */
    public function testAdminImagesIndexAction()
    {
        $this->dispatch('/pages/images/');
        $this->assertModule('pages');
        $this->assertController('images');
        $this->assertAction('index');
    }
    
    /**
     * Admin/Images/Manager
     */
    public function testAdminImagesManagerAction()
    {
        $this->dispatch('/pages/images/manager/');
        $this->assertModule('pages');
        $this->assertController('images');
        $this->assertAction('manager');
    }
    
    /**
     * Admin/Images/Browser
     */
    public function testAdminImagesBrowserAction()
    {
        $this->dispatch('/pages/images/browser/');
        $this->assertModule('pages');
        $this->assertController('images');
        $this->assertAction('browser');
    }
    
    /**
     * Admin/Images/Delete
     */
    public function testAdminImagesDeleteAction()
    {
        $this->dispatch('/pages/images/delete/');
        $this->assertModule('default');
        $this->assertController('error');
        $this->assertAction('internal');
    }
    
    /**
     * Admin/Images/Upload
     */
    public function testAdminImagesUploadAction()
    {
        $this->dispatch('/pages/images/upload/');
        $this->assertModule('default');
        $this->assertController('error');
        $this->assertAction('internal');
    }
    
    /**
     * Admin/Images/Thumb
     */
    public function testAdminImagesThumbAction()
    {
        $this->dispatch('/pages/images/thumb/');
        $this->assertModule('pages');
        $this->assertController('images');
        $this->assertAction('thumb');
    }
}