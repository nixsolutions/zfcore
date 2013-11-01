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
    public function testIndexActionForGuest()
    {
        //for guest
        $this->_doLogin(Users_Model_User::ROLE_GUEST);
        $this->dispatch('/subscriptions');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertContains('<h3>Subscriptions</h3>', $this->getResponse()->getBody());
    }

    public function testIndexActionForUserWithoutSubscription()
    {
        //for user
        $this->_doLogin(Users_Model_User::ROLE_USER);
        $this->dispatch('/subscriptions');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertContains('<h3>Subscriptions</h3>', $this->getResponse()->getBody());
    }


    public function testIndexActionForUserWithSubscription()
    {
        $planTypeId = 1;
        //Create user
        $account = new Users_Model_User();
        $account->avatar = null;
        $account->login = 'testIndexActionForUserWithSubscription' . date('YmdHis');
        $account->email = 'testIndexActionForUserWithSubscription' . time() . '@example.org';
        $account->password = md5('password');
        $account->role = Users_Model_User::ROLE_USER;
        $account->status = Users_Model_User::STATUS_ACTIVE;
        $account->save();
        //Login
        Zend_Auth::getInstance()->getStorage()->write($account);

        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
        $subscriptionManager->createFreeSubscription($account->id, $planTypeId);

        $this->dispatch('/subscriptions');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertContains('Current plan', $this->getResponse()->getBody());
    }


    public function testIndexActionForUserWithSubscriptionException()
    {
        $planTypeId = 1;
        //Create user
        $account = new Users_Model_User();
        $account->avatar = null;
        $account->login = 'testIndexActionForUserWithSubscriptionException' . date('YmdHis');
        $account->email = 'testIndexActionForUserWithSubscriptionException' . time() . '@example.org';
        $account->password = md5('password');
        $account->role = Users_Model_User::ROLE_USER;
        $account->status = Users_Model_User::STATUS_ACTIVE;
        $account->save();
        //Login
        Zend_Auth::getInstance()->getStorage()->write($account);

        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
        $subscriptionManager->createFreeSubscription($account->id, $planTypeId);

        //Test empty config
        Zend_Registry::set('payments', null);

        $this->dispatch('/subscriptions');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains('<b>Message:</b> Paypal is not configured.</p>', $this->getResponse()->getBody());
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


    public function testCreateActionByUserByGetMethod()
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


    public function testCreateActionByUserTrialPlan()
    {
        //POST
        //Save fake user to DB
        $account = new Users_Model_User();
        $account->avatar = null;
        $account->login = 'testCreateActionByUserTrialPlan' . date('YmdHis');
        $account->email = 'testCreateActionByUserTrialPlan' . time() . '@example.org';
        $account->password = md5('password');
        $account->role = Users_Model_User::ROLE_USER;
        $account->status = Users_Model_User::STATUS_ACTIVE;
        $account->save();
        //Login
        Zend_Auth::getInstance()->getStorage()->write($account);

        //Get plan
        $subscriptionPlansTable = new Subscriptions_Model_SubscriptionPlans_Table();
        $subscriptionPlan = $subscriptionPlansTable->getByType(Subscriptions_Model_SubscriptionPlan::PLAN_TYPE_TRIAL);

        $this->request
             ->setMethod('POST')
             ->setPost(array('id' => $subscriptionPlan->id));
        $this->dispatch('/subscriptions/index/create');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('create');
        $this->assertRedirectTo('/subscriptions/index/complete');

        $this->resetResponse();

        //Test create another Trial plan
        $this->request
             ->setMethod('POST')
             ->setPost(array('id' => $subscriptionPlan->id));
        $this->dispatch('/subscriptions/index/create');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('create');
        $this->assertRedirectTo('/subscriptions');
    }


    public function testCreateActionByUserNotTrialPlan()
    {
        //POST
        //Save fake user to DB
        $account = new Users_Model_User();
        $account->avatar = null;
        $account->login = 'testCreateActionByUserNotTrialPlan' . date('YmdHis');
        $account->email = 'testCreateActionByUserNotTrialPlan' . time() . '@example.org';
        $account->password = md5('password');
        $account->role = Users_Model_User::ROLE_USER;
        $account->status = Users_Model_User::STATUS_ACTIVE;
        $account->save();
        //Login
        Zend_Auth::getInstance()->getStorage()->write($account);

        //Get plan
        $subscriptionPlansTable = new Subscriptions_Model_SubscriptionPlans_Table();
        $subscriptionPlan = $subscriptionPlansTable->getByType(Subscriptions_Model_SubscriptionPlan::PLAN_TYPE_MONTHLY);

        $this->request
             ->setMethod('POST')
             ->setPost(array('id' => $subscriptionPlan->id));
        $this->dispatch('/subscriptions/index/create');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('create');
    }


    public function testPlanInfoAction()
    {
        //Save fake user to DB
        $account = new Users_Model_User();
        $account->avatar = null;
        $account->login = 'testPlanInfoAction' . date('YmdHis');
        $account->email = 'testPlanInfoAction' . time() . '@example.org';
        $account->password = md5('password');
        $account->role = Users_Model_User::ROLE_USER;
        $account->status = Users_Model_User::STATUS_ACTIVE;
        $account->save();
        //Login
        Zend_Auth::getInstance()->getStorage()->write($account);

        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
        $subscriptionManager->createFreeSubscription($account->id, 1);

        $this->dispatch('/subscriptions/index/plan-info');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('plan-info');
        $this->assertContains('Current plan', $this->getResponse()->getBody());
    }

    public function testPlanInfoActionException()
    {
        $this->_doLogin(Users_Model_User::ROLE_USER);
        $this->dispatch('/subscriptions/index/plan-info');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains('<b>Message:</b> Page not found</p>', $this->getResponse()->getBody());
    }


    public function testCompleteAction()
    {
        //Save fake user to DB
        $account = new Users_Model_User();
        $account->avatar = null;
        $account->login = 'testCompleteAction' . date('YmdHis');
        $account->email = 'testCompleteAction' . time() . '@example.org';
        $account->password = md5('password');
        $account->role = Users_Model_User::ROLE_USER;
        $account->status = Users_Model_User::STATUS_ACTIVE;
        $account->save();
        //Login
        Zend_Auth::getInstance()->getStorage()->write($account);

        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
        $subscriptionManager->createFreeSubscription($account->id, 1);

        $this->dispatch('/subscriptions/index/complete');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('complete');
        $this->assertContains('Current plan', $this->getResponse()->getBody());
    }


    public function testCompleteActionException()
    {
        $this->_doLogin(Users_Model_User::ROLE_USER);
        $this->dispatch('/subscriptions/index/complete');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains('<b>Message:</b> Page not found</p>', $this->getResponse()->getBody());
    }


    /**
     * Index action
     */
    public function testCompletePaymentActionAction()
    {
        $this->dispatch('/subscriptions/index/complete-payment');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('complete-payment');
        $this->assertRedirectTo('/subscriptions');
    }


    public function testCanceledPaymentActionn()
    {
        $this->dispatch('/subscriptions/index/canceled-payment');
        $this->assertModule('subscriptions');
        $this->assertController('index');
        $this->assertAction('canceled-payment');
        $this->assertRedirectTo('/subscriptions');
    }


}
