<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Forum_PostControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->_fixture['post'] = array('id' => 45,
                                   'title' => 'title',
                                   'body' => 'text',
                                   'categoryId' => 33,
                                   'userId' => 75,
                                   'status' => 'active');

        $this->_fixture['category'] = array('id' => 33,
                                   'title' => 'title',
                                   'description' => 'descr',
                                   'parentId' => 0,
                                   'alias' => 'title');

        $users= new Users_Model_User_Table();
        $user = $users->createRow(
            array(
                 'id'       => 75,
                 'login'    => 'asdasd',
                 'email'    => 'test@email.com',
                 'password' => '123456'
            )
        );
        $user->save();
    }

    public function testEmptyPostAction()
    {
        $this->dispatch('/forum/post/');
        $this->assertModule('users');
        $this->assertController('error');
        $this->assertAction('error');
    }

    public function testIndexAction()
    {
        $table = new Forum_Model_Post_Table();
        $manager = new Forum_Model_Category_Manager();
        $rootCat = $manager->getRoot();

        $cat = $manager->getDbTable()->createRow($this->_fixture['category']);
        $rootCat->addChild($cat);

        $post = $table->createRow($this->_fixture['post']);
        $post->save();

        $this->dispatch('/forum/post/45');
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

        $post = $table->createRow($this->_fixture['post']);
        $post->save();

        $this->_doLogin();

        $this->request->setMethod('POST')
                      ->setPost(array('comment' => 'comment'));

        $this->dispatch('/forum/post/45');
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
        $cat = $manager->getDbTable()->createRow(
            array(
                'id' => 33,
                'title' => 'title',
                'description' => 'descr',
                'parentId' => 0,
                'alias' => 'sdasfs'
            )
        );
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

        $this->dispatch('/forum/post/create/');
        $this->assertModule('forum');
        $this->assertController('post');
        $this->assertAction('create');

        $cat->delete();
    }

    public function testEditAction()
    {
        $table = new Forum_Model_Post_Table();

        $manager = new Forum_Model_Category_Manager();

        $rootCat = $manager->getRoot();
        $cat = $manager->getDbTable()->createRow($this->_fixture['category']);
        $rootCat->addChild($cat);

        $post = $table->createRow($this->_fixture['post']);
        $post->save();

        $this->_doLogin();

        $this->dispatch('/forum/post/edit/45');
        $this->assertModule('forum');
        $this->assertController('post');
        $this->assertAction('edit');

        $post->delete();
        $cat->delete();
    }

    public function testEditWithDataAction()
    {
        $table = new Forum_Model_Post_Table();
        $manager = new Forum_Model_Category_Manager();

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
                              'categoryId' => 33,
                              'status'   => 'active'
                          )
                      );

        $this->dispatch('/forum/post/edit/45');
        $this->assertModule('forum');
        $this->assertController('post');
        $this->assertAction('edit');
        $this->assertRedirect();

        $post->delete();
        $cat->delete();
    }

    public function tearDown()
    {
        $table= new Users_Model_User_Table();
        $table->delete('1');

        $table = new Forum_Model_Post_Table();
        $table->delete('1');

        $table = new Categories_Model_Category_Table();
        $table->delete('id = 33');

        parent::tearDown();
    }
}