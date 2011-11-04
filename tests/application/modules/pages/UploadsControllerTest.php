<?php
/**
 * Pages_UploadsControllerTest
 *
 * @category Tests
 * @package  Pages
 */
class Pages_UploadsControllerTest extends ControllerTestCase
{
    /**
     * Setup TestCase
     */
    public function setUp()
    {
        parent::setUp();
        parent::_doLogin(Users_Model_User::ROLE_ADMIN);
    }

    /**
     * Admin/Images/Index
     */
    public function testAdminImagesIndexAction()
    {
        $this->dispatch('/pages/uploads/');
        $this->assertModule('pages');
        $this->assertController('uploads');
        $this->assertAction('index');
    }
}