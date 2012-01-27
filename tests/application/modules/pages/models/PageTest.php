<?php
/**
 * PagesTest
 *
 * @category Tests
 * @package  Model
 */
class Model_PageTest extends ControllerTestCase
{
    /**
     * set up environment
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        //parent::migrationUp('pages');
    }
    /**
     * Fixtures
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Set up environment
     *
     */
    public function setUp()
    {
        $uid = date('Y-m-d-H:i:s');

        $this->_data = array(
            'pid'   => 123,
            'title' => 'Test: ' . $uid,
            'alias' => 'test-' . $uid,
            'content' => '<h2>Test</h2><p>Content, content, content</p>',
            'user_id' => 1,
        );
        parent::setUp();
    }

    /**
     * Test create page
     *
     */
    public function testPageCreate()
    {
        $page = new Pages_Model_Page();

        $page->setFromArray($this->_data);

        $this->assertTrue(($page instanceof Core_Db_Table_Row_Abstract ));

        try {
            $page->save();
            $page->delete();
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Test create page
     *
     */
    function testPageCreate2()
    {
        $page = new Pages_Model_Page($this->_data);

        $this->assertTrue(($page instanceof Core_Db_Table_Row_Abstract ));
    }

    function testPageCreate3()
    {
        $page = new Pages_Model_Page();
        $page->title   = $this->_data['title'];
        $page->alias   = $this->_data['alias'];
        $page->content = $this->_data['content'];
        $page->pid     = $this->_data['pid'];

        $this->assertTrue(($page instanceof Core_Db_Table_Row_Abstract ));

        try {
            $page->save();
            $page->delete();
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    function testPageToArray()
    {
        $page = new Pages_Model_Page();
        $page->setFromArray($this->_data);

        $this->assertEquals(10, sizeof($page->toArray()));

        try {
            $page->save();
            $page->delete();
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    function testPageDelete()
    {
        $page = new Pages_Model_Page();
        $page->setFromArray($this->_data);

        try {
            $page->save();
            $page->delete();
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        // new page instance
        $pageTable = new Pages_Model_Page_Table();

        // Get Record from DB
        $pages = $pageTable->getByAlias($this->_data['alias']);

        $this->assertEquals(0, sizeof($pages));
    }



    public static function tearDownAfterClass()
    {
        //parent::migrationDown('pages');
        parent::tearDownAfterClass();
    }
}
