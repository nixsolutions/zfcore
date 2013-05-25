<?php
/**
 * User: naxel
 * Date: 25.05.13 17:51
 */

/**
 * ManagementControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Pages_ManagementControllerTest extends ControllerTestCase
{

    /**
     * set up environment
     */
    public function setUp()
    {
        parent::setUp();
        parent::_doLogin(Users_Model_User::ROLE_ADMIN);

        $data = array(
            'id' => 666,
            'title' => 'Test' . date('Y-m-d H:i:s'),
            'alias' => 'test',
            'content' => '<p>Content, content, content</p>',
            'user_id' => 1,
            'pid' => 1);

        $manager = new Pages_Model_Page_Table();
        // Insert record to DB
        $page = $manager->createRow($data);
        $page->save();
    }


    public function testIndex()
    {
        $this->dispatch('/pages/management');
        $this->assertModule('pages');
        $this->assertController('management');
        $this->assertAction('index');
    }


    public function testEdit()
    {
        $this->dispatch('/pages/management/edit/id/1');
        $this->assertModule('pages');
        $this->assertController('management');
        $this->assertAction('edit');
    }


    public function tearDown()
    {
        $table = new Pages_Model_Page_Table();
        $table->delete('id = 666');
        parent::tearDown();
    }

}
