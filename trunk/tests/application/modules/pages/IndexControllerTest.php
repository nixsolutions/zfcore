<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Pages_IndexControllerTest extends ControllerTestCase
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
     * set up environment
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->_data = array('title'   => 'Test'.date('Y-m-d H:i:s'),
                             'alias'   => 'test',
                             'content' => '<p>Content, content, content</p>',
                             'user_id' => 1,
                             'pid'     => 1);
    }

    public function testAboutPage()
    {
        $manager = new Pages_Model_Page_Table();
        
        // Insert record to DB
        $page = $manager->create($this->_data);
        
        $page->save();

        $this->dispatch('/'.$this->_data['alias'].'.html');
        $this->assertModule('pages');
        $this->assertController('index');
        $this->assertAction('index');
        
        $page->delete();
    }

    public function testErrorPage()
    {
        $this->dispatch('/error-page.html');
        $this->assertModule('default');
        $this->assertController('error');
        $this->assertAction('notfound');
    }
    
    public function testSitemapPage()
    {
        $this->dispatch('/sitemap.html');
        $this->assertModule('pages');
        $this->assertController('index');
        $this->assertAction('sitemap');
    }
    
    public function testSitemapXmlPage()
    {
        $this->dispatch('/sitemap.xml');
        $this->assertModule('pages');
        $this->assertController('index');
        $this->assertAction('sitemapxml');
        
//        $this->assertHeader('Content-Type', 'application/xml');
    }


    public static function tearDownAfterClass()
    {
        parent::migrationDown('pages');
        parent::tearDownAfterClass();
    }
}

