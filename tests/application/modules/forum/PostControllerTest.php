<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Forum_PostControllerTest extends ControllerTestCase
{

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        parent::migrationUp('forum');
    }

    public function setUp()
    {
        parent::setUp();

        $this->_fixture['post'] = array('id' => 45,
                                   'post_title' => 'title',
                                   'post_text' => 'text',
                                   'ctg_id' => 33,
                                   'user_id' => 75,
                                   'post_status' => 'active');

        $this->_fixture['category'] = array('id' => 33,
                                   'title' => 'title',
                                   'description' => 'descr',
                                   'parentId' => 0,
                                   'alias' => 'title');
    }

    public function testEmptyPostAction()
    {
        $this->dispatch('/forum/post/');
        $this->assertModule('forum');
        $this->assertController('post');
        $this->assertAction('index');
        $this->assertRedirect('/');
    }

    public function testIndexAction()
    {
        $table = new Forum_Model_Post_Table();
        $manager = new Forum_Model_Category_Manager();
        $rootCat = $manager->getRoot();

        $cat = $manager->getDbTable()->createRow($this->_fixture['category']);
        $rootCat->addChild($cat);

        $post = $table->create($this->_fixture['post']);
        $post->save();

        $this->dispatch('/forum/post/index/id/45');
        $this->assertModule('forum');
        $this->assertController('post');
        $this->assertAction('index');

        $post->delete();
        $cat->delete();
    }

    public function testCreateCommentIndexAction()
    {
        $table = new Forum_Model_Post_Table();
        $manager = new Forum_Model_Category_Manager();

        $rootCat = $manager->getRoot();
        $cat = $manager->getDbTable()->createRow($this->_fixture['category']);
        $rootCat->addChild($cat);

        $post = $table->create($this->_fixture['post']);
        $post->save();

        $this->_doLogin();

        $this->request->setMethod('POST')
                      ->setPost(array('comment' => 'comment'));

        $this->dispatch('/forum/post/index/id/45');
        $this->assertModule('forum');
        $this->assertController('post');
        $this->assertAction('index');

        $post->delete();
        $cat->delete();
    }

    public function testCreateAction()
    {
        $this->dispatch('/forum/post/create/');
        $this->assertModule('forum');
        $this->assertController('post');
        $this->assertAction('create');
    }

    public function testCreateWithDataAction()
    {
        $manager = new Forum_Model_Category_Manager();

        $rootCat = $manager->getRoot();
        $cat = $manager->getDbTable()->createRow(array(
            'id' => 33,
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

        $this->dispatch('/forum/post/create/');
        $this->assertModule('forum');
        $this->assertController('post');
        $this->assertAction('create');

        $cat->delete();
    }

    //FIXME: user ID is not set after creation
    /*public function testEditAction()
    {
        $table = new Forum_Model_Post_Table();
        $tableCat = new Forum_Model_Category_Table();

        $cat = $tableCat->create($this->_fixture['category']);
        $cat->save();

        $post = $table->create($this->_fixture['post']);
        $post->save();

        //Test don't work becouse user is not author and is forwarded
        //TODO: check creation
        $this->dispatch('/forum/post/edit/id/45');
        $this->assertModule('forum');
        $this->assertController('post');
        $this->assertAction('edit');

        $post->delete();
        $cat->delete();
    }*/

    public function testEditWithDataAction()
    {
        $table = new Forum_Model_Post_Table();
        $manager = new Forum_Model_Category_Manager();

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
                              'category' => 33,
                              'status'   => 'active'
                          )
                      );

        $this->dispatch('/forum/post/edit/id/45');
        $this->assertModule('forum');
        $this->assertController('post');
        $this->assertAction('edit');
        $this->assertRedirect();

        $post->delete();
        $cat->delete();
    }

    public static function tearDownAfterClass()
    {
        parent::migrationDown('forum');
        parent::tearDownAfterClass();
    }
}