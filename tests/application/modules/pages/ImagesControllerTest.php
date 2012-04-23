<?php
/**
 * Pages_UploadsControllerTest
 *
 * @category Tests
 * @package  Pages
 */
class Pages_ImagesControllerTest extends ControllerTestCase
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
     * Admin/Pages/Images
     */
    public function testAdminImagesListAction()
    {
        $this->dispatch('/pages/images/list');
        $this->assertModule('pages');
        $this->assertController('images');
        $this->assertAction('list');
    }
}