<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Forum_CategoryControllerTest extends ControllerTestCase
{
    
    /**
     * set up environment
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        parent::migrationUp('forum');
    }

    public function setUp()
    {
        parent::setUp();
        $this->_fixture['post'] = array(
                                   'post_title' => 'title',
                                   'post_text' => 'text',
                                   'ctg_id' => 32,
                                   'user_id' => 207,
                                   'post_status' => 'active');

        $this->_fixture['category'] = array('id' => 32,
                                   'ctg_title' => 'title',
                                   'ctg_description' => 'descr',
                                   'ctg_parent_id' => 0);


        $tableCat = new Forum_Model_Category_Table();
        $cat = $tableCat->create($this->_fixture['category']);
        $cat->save();

        $table = new Forum_Model_Post_Table();
        for ($i = 0; $i < 15; $i++) {
            $post = $table->create($this->_fixture['post']);
            $post->save();
        }
    }

    //FIXME:
    public function testBlogAction()
    {
        $this->dispatch('/forum/category/blog');
        $this->assertModule('forum');
        $this->assertController('category');
        $this->assertAction('blog');
    }
    
    public function testForumCatsAction()
    {
        $this->dispatch('/forum/category/forum/id/32');
        $this->assertModule('forum');
        $this->assertController('category');
        $this->assertAction('forum');
    }
    
    public function testForumAction()
    {
        $this->dispatch('/forum/category/forum/');
        $this->assertModule('forum');
        $this->assertController('category');
        $this->assertAction('forum');
    }

    public function tearDown()
    {
        $tablePost = new Forum_Model_Post_Table();
        $tablePost->delete('');
        $tableCat = new Forum_Model_Category_Table();
        $tableCat->delete('');
        parent::tearDown();
    }


    public static function tearDownAfterClass()
    {
        parent::migrationDown('forum');
        parent::tearDownAfterClass();
    }
}