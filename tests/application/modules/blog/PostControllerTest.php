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
        parent::migrationUp('comments');
    }

    public function setUp()
    {
        parent::setUp();

        $this->_fixture['post'] = array(
            'id'         => 55,
            'title'      => 'title',
            'body'       => 'text',
            'categoryId' => 43,
            'alias'      => 'test1',
            'userId'     => 75,
            'status'     => 'published'
        );
        $this->_fixture['post2'] = array(
            'id'         => 56,
            'title'      => 'title2',
            'body'       => 'text2',
            'categoryId' => 44,
            'alias'      => 'test2',
            'userId'     => 75,
            'status'     => 'published'
        );

        $this->_fixture['category'] = array(
            'id'          => 43,
            'title'       => 'title',
            'description' => 'descr',
            'parentId'    => 0,
            'alias'       => 'title'
        );
        $this->_fixture['category2'] = array(
            'id'          => 44,
            'title'       => 'title2',
            'description' => 'descr2',
            'parentId'    => 0,
            'alias'       => 'title2'
        );
        $this->_fixture['user'] = array(
            'id'       => 75,
            'login'    => 'test_login',
            'password' => '123456',
            'email'    => 'test@email.com'
        );
        $users = new Users_Model_Users_Table();

        $users->delete(' id = ' . $this->_fixture['user']['id']);
        $user = $users->createRow($this->_fixture['user']);
        $user->save();
    }

    public function testEmptyPostAction()
    {
        $this->dispatch('/blog/post/');
        $this->assertModule('users');
        $this->assertController('error');
        $this->assertAction('error');
    }

    public function testIndexAction()
    {
        $table = new Blog_Model_Post_Table();
        $manager = new Blog_Model_Category_Manager();
        $rootCat = $manager->getRoot();

        $this->_doLogin();

        $cat = $manager->getDbTable()->createRow($this->_fixture['category']);
        $rootCat->addChild($cat);

        $post = $table->createRow($this->_fixture['post']);
        $post->save();

        $this->dispatch('/blog/post/' . $post->alias);

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
        $cat = $manager->getDbTable()->createRow($this->_fixture['category2']);
        $rootCat->addChild($cat);

        $post = $table->createRow($this->_fixture['post2']);
        $post->save();

        $this->_doLogin();

        $this->request->setMethod('POST')
                      ->setPost(array('comment' => 'comment'));

        $this->dispatch('/blog/post/' . $this->_fixture['post2']['alias']);
        //var_dump($this->getResponse()->getBody());exit;
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
            'id' => 73,
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
                              'body' => 'text',
                              'categoryId' => 33,
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

        $post = $table->createRow($this->_fixture['post']);
        $post->save();

        $this->_doLogin();

        $this->request->setMethod('POST')
                      ->setPost(
                          array(
                              'title'    => 'tttttttt',
                              'body'     => 'tttttttt',
                              'categoryId' => 93,
                              'status'   => 'draft'
                          )
                      );

        $this->dispatch('/blog/post/edit/' . $this->_fixture['post']['alias']);
        $this->assertModule('blog');
        $this->assertController('post');
        $this->assertAction('edit');

        $post->delete();
        $cat->delete();
    }

    public function tearDown()
    {
        $table = new Blog_Model_Post_Table();
        $table->delete('1');

        $table = new Categories_Model_Category_Table();
        $table->delete(' id = 43');

        $table = new Users_Model_Users_Table();
        $table->delete(' id = ' . $this->_fixture['user']['id']);
    }

    public static function tearDownAfterClass()
    {
        parent::migrationDown('blog');
        parent::migrationDown('comments');
        parent::tearDownAfterClass();
    }
}