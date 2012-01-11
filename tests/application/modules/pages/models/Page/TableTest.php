<?php
/**
 * PagesTest
 *
 * @category Tests
 * @package  Model
 */
class Model_Page_ManagerTest extends ControllerTestCase
{
    /**
     * set up environment
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        parent::migrationUp('pages');
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
        parent::setUp();

        $uid = date('Y-m-d-H:i:s');

        $this->_data = array(
            'pid'     => 123,
            'title'   => 'Test: ' . $uid,
            'alias'   => 'test-' . $uid,
            'content' => '<h2>Test</h2><p>Content, content, content</p>',
            'user_id' => 1,
        );
        $this->_pageTable = new Pages_Model_Page_Table();
    }

    /**
     * Test create page
     *
     */
    public function testPageCreate()
    {
        $page = $this->_pageTable->createRow($this->_data);

        $this->assertTrue(($page instanceof Core_Db_Table_Row_Abstract));
    }

    /**
     * Enter description here...
     *
     */
    function testPageFind()
    {
        $page = $this->_pageTable->createRow($this->_data);
        $page->save();

        $this->assertNotNull($page->id);

        // Get Record from DB
        $page = $this->_pageTable->find($page->id);

        $this->assertTrue(($page instanceof Zend_Db_Table_Rowset));
        $this->assertEquals($this->_data['title'], $page['0']->title);

        try {
            $page['0']->delete();
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    function testPageGetByAlias()
    {
        $page = $this->_pageTable->createRow($this->_data);
        $page->save();

        $this->assertNotNull($page->id);

        // Get Record from DB
        $page = $this->_pageTable->getByAlias($this->_data['alias']);

        $this->assertTrue(($page instanceof Core_Db_Table_Row_Abstract));
        $this->assertEquals($this->_data['title'], $page->title);

        try {
            $page->delete();
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Test deleteById
     * FIXME: Undefined property: Zend_Db_Table_Rowset::$id
     */
    function testPageDeleteById()
    {
        $page = $this->_pageTable->createRow($this->_data);
        $page->save();

        $pageId = $page->id;

        $this->assertNotNull($pageId);

        // Delete Record in DB
        $res = $this->_pageTable->deleteById($pageId);
        $this->assertEquals(1, $res);

        // Get Record from DB
        //$page = $this->_pageTable->find($pageId);
        //$this->assertNull($page->id);
    }


    public static function tearDownAfterClass()
    {
        parent::migrationDown('pages');
        parent::tearDownAfterClass();
    }
}
