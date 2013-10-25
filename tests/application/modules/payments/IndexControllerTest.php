<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Payments
 */
class Payments_IndexControllerTest extends ControllerTestCase
{

    /**
     * set up environment
     */
    public function setUp()
    {
        parent::setUp();
        parent::_doLogin(Users_Model_User::ROLE_USER);
    }


    public function testIndexActionByGuest()
    {
        //for guest
        $this->_doLogin(Users_Model_User::ROLE_GUEST);

        $this->dispatch('/payments/index/create');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('denied');
    }


    public function testIndexActionByUserEmptyRequest()
    {
        //for user
        $this->_doLogin(Users_Model_User::ROLE_USER);

        $this->dispatch('/payments/index/create');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains('<b>Message:</b> Page not found</p>', $this->getResponse()->getBody());

    }


    public function testIndexActionByUserIncorrectOrder()
    {
        $this->_doLogin(Users_Model_User::ROLE_USER);
        //Test incorrect order
        $this->resetResponse();

        $this->request
             ->setMethod('GET')
             ->setPost(array('orderId' => 111111111111111111, 'callFrom' => 'view'));
        $this->dispatch('/payments/index/create');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains('<b>Message:</b> Page not found</p>', $this->getResponse()->getBody());
    }


    public function testIndexActionByUserCorrectOrder()
    {
        //Fake data
        $price = '50';

        //Create user
        $account = new Users_Model_User();
        $account->avatar = null;
        $account->login = 'testIndexActionByUserCorrectOrder' . date('YmdHis');
        $account->email = 'testIndexActionByUserCorrectOrder' . time() . '@example.org';
        $account->password = md5('password');
        $account->role = Users_Model_User::ROLE_USER;
        $account->status = Users_Model_User::STATUS_ACTIVE;
        $account->save();

        //Create order
        $orderManager = new Payments_Model_Order_Manager();
        $order = $orderManager->createOrder($account->id, $price);
        //Test correct order
        $this->resetResponse();

        $this->request
             ->setMethod('GET')
             ->setPost(array('orderId' => $order->id, 'callFrom' => 'view'));
        $this->dispatch('/payments/index/create');
        $this->assertModule('payments');
        $this->assertController('index');
        $this->assertAction('create');

    }


    public function testEmptyConfig()
    {
        //Removing config
        Zend_Registry::set('payments', null);

        $this->dispatch('/payments/index/create');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains('<b>Message:</b> Paypal is not configured.</p>', $this->getResponse()->getBody());
    }


    /**
     * Index action
     */
    public function testCompleteAction()
    {
        $this->dispatch('/payments/index/complete');
        $this->assertModule('payments');
        $this->assertController('index');
        $this->assertAction('complete');
        $this->assertRedirectTo('/subscriptions');
    }


    public function testCanceledAction()
    {
        $this->dispatch('/payments/index/canceled');
        $this->assertModule('payments');
        $this->assertController('index');
        $this->assertAction('canceled');
        $this->assertRedirectTo('/subscriptions');
    }

}
