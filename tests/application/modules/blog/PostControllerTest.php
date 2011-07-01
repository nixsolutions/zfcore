<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Blog_PostControllerTest extends ControllerTestCase
{

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        parent::migrationUp('blog');
    }

    public function setUp()
    {
        parent::setUp();

        $this->_fixture['post'] = array('id' => 55,
                                   'post_title' => 'title',
                                   'post_text' => 'text',
                                   'ctg_id' => 33,
                                   'user_id' => 75,
                                   'post_status' => 'active');

        $this->_fixture['category'] = array('id' => 43,
                                   'title' => 'title',
                                   'description' => 'descr',
                                   'parentId' => 0,
                                   'alias' => 'title');
    }

    public function testEmptyPostAction()
    {
        $this->dispatch('/blog/post/');
        $this->assertModule('blog');
        $this->assertController('post');
        $this->assertAction('index');
        $this->assertRedirect('/');
    }

    public function testIndexAction()
    {
        $table = new Blog_Model_Post_Table();
        $manager = new Blog_Model_Category_Manager();
        $rootCat = $manager->getRoot();

        $cat = $manager->getDbTable()->createRow($this->_fixture['category']);
        $rootCat->addChild($cat);

        $post = $table->create($this->_fixture['post']);
        $post->save();

        $this->dispatch('/blog/post/index/id/55');
        $this->assertModule('blog');
        $this->assertController('post');
        $this->assertAction('index');

        $post->delete();
        $cat->delete();
    }

    public function testCreateCommentIndexAction()
    {
        $table = new Blog_Model_Post_Table();
        $manager = new Blog_Model_Category_Manager();

        $rootCat = $manager->getRoot();
        $cat = $manager->getDbTable()->createRow($this->_fixture['category']);
        $rootCat->addChild($cat);

        $post = $table->create($this->_fixture['post']);
        $post->save();

        $this->_doLogin();

        $this->request->setMethod('POST')
                      ->setPost(array('comment' => 'comment'));

        $this->dispatch('/blog/post/index/id/55');
        $this->assertModule('blog');
        $this->assertController('post');
        $this->assertAction('index');

        $post->delete();
        $cat->delete();
    }

    public function testCreateAction()
    {
        $this->dispatch('/blog/post/create/');
        $this->assertModule('blog');
        $this->assertController('post');
        $this->assertAction('create');
    }

    public function testCreateWithDataAction()
    {
        $manager = new Blog_Model_Category_Manager();

        $rootCat = $manager->getRoot();
        $cat = $manager->getDbTable()->createRow(array(
            'id' => 43,
            'title' => 'title',
            'description' => 'descr',
            'parentId' => 0,
            'alias' => 'sdasfs'
        ));
        $rootCat->addChild($cat);


        $this->_doLogin();

        $this->request->setMethod('POST')
                      ->setPost(
                          array(
                              'title' => 'title',
                              'text' => 'text',
                              'category' => 33,
                              'status' => 'active'
                          )
                      );

        $this->dispatch('/blog/post/create/');
        $this->assertModule('blog');
        $this->assertController('post');
        $this->assertAction('create');

        $cat->delete();
    }

    public function testEditWithDataAction()
    {
        $table = new Blog_Model_Post_Table();
        $manager = new Blog_Model_Category_Manager();

        $rootCat = $manager->getRoot();
        $cat = $manager->getDbTable()->createRow($this->_fixture['category']);
        $rootCat->addChild($cat);

        $post = $table->create($this->_fixture['post']);
        $post->save();

        $this->_doLogin();

        $this->request->setMethod('POST')
                      ->setPost(
                          array(
                              'title'    => 'tttttttt',
                              'text'     => 'tttttttt',
                              'category' => 43,
                              'status'   => 'active'
                          )
                      );

        $this->dispatch('/blog/post/edit/id/55');
        $this->assertModule('blog');
        $this->assertController('post');
        $this->assertAction('edit');
        $this->assertRedirect();

        $post->delete();
        $cat->delete();
    }

    public static function tearDownAfterClass()
    {
        parent::migrationDown('blog');
        parent::tearDownAfterClass();
    }
}