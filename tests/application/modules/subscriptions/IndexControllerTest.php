<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Subscriptions
 */
class Subscriptions_IndexControllerTest extends ControllerTestCase
{

    /**
     * set up environment
     */
    public function setUp()
    {
        parent::setUp();
        parent::_doLogin(Users_Model_User::ROLE_USER);
    }

    /**
     * Index action
     */
    public function testIndexAction()
    {
        //for guest
        $this->_doLogin(Users_Model_User::ROLE_GUEST);
        $this->dispatch('/subscriptions');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertContains('<h3>Subscriptions</h3>', $this->getResponse()->getBody());

        //for user
        $this->_doLogin(Users_Model_User::ROLE_USER);
        $this->dispatch('/subscriptions');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertContains('<h3>Subscriptions</h3>', $this->getResponse()->getBody());
    }


    public function testCreateActionByGuest()
    {
        //for guest
        $this->_doLogin(Users_Model_User::ROLE_GUEST);

        //GET
        $this->dispatch('/subscriptions/index/create');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('denied');

        //POST
        $this->request
             ->setMethod('POST')
             ->setPost(array('id' => 1));
        $this->dispatch('/subscriptions/index/create');

        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('denied');
    }


    public function testCreateActionByUserGet()
    {
        //for user
        $this->_doLogin(Users_Model_User::ROLE_USER);

        //GET
        $this->dispatch('/subscriptions/index/create');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('create');
        $this->assertRedirectTo('/subscriptions');
    }


    public function testCreateActionByUser()
    {
        //POST
        //Save fake user to DB
        //Fake data
        $userId = '123455';
        //Create user
        $account = new Users_Model_User();
        $account->avatar = null;
        $account->login = 'AutoTest2' . date('YmdHis');
        $account->email = 'autotest2' . time() . '@example.org';
        $account->password = md5('password');
        $account->role = Users_Model_User::ROLE_USER;
        $account->status = Users_Model_User::STATUS_ACTIVE;
        $account->id = $userId;
        $account->save();
        //Login
        Zend_Auth::getInstance()->getStorage()->write($account);

        $this->request
             ->setMethod('POST')
             ->setPost(array('id' => 1));
        $this->dispatch('/subscriptions/index/create');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('create');
        $this->assertRedirectTo('/subscriptions/index/complete');

        $this->resetResponse();
        //GET
        $this->dispatch('/subscriptions/index/complete');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('complete');
        $this->assertContains('<h3>Thank you</h3>', $this->getResponse()->getBody());

        $this->resetResponse();
        //GET plan-info
        $this->dispatch('/subscriptions/index/plan-info');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('plan-info');
        $this->assertContains('Current plan', $this->getResponse()->getBody());
    }


}
