<?php
/**
 * Class Subscriptions_Model_Subscription_ManagerTest
 */
class Subscriptions_Model_Subscription_ManagerTest extends ControllerTestCase
{

    public function setUp()
    {
        parent::setUp();
        parent::_doLogin(Users_Model_User::ROLE_USER);
    }


    public function testCreateAndCancelSubscriptionByPaypalCustomParam()
    {
        //Fake data
        $orderType = Payments_Model_Order::ORDER_TYPE_SUBSCRIPTION;
        $userId = '123456';
        $planId = '3';
        $price = '50';
        $payPalSubscriptionId = 'paypal';

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

        //Create order
        $orderManager = new Payments_Model_Order_Manager();
        $order = $orderManager->createOrder($userId, $price);

        //Create custom param
        $customParam = implode('-', array($orderType, $order->id, $userId, $planId));

        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
        $subscription = $subscriptionManager->createSubscriptionByPaypalCustomParam($customParam, $payPalSubscriptionId);

        $this->assertNotEmpty($subscription);

        $response = $subscriptionManager->cancelSubscriptionByPaypalCustomParam($customParam, $payPalSubscriptionId);
        $this->assertTrue($response);
    }


    public function testCreateSubscription()
    {
        //Fake data
        $userId = '1234567';
        $planId = '3';
        //set one day expired date
        $expirationDate = date('Y-m-d H:i:s', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));

        //Create user
        $account = new Users_Model_User();
        $account->avatar = null;
        $account->login = 'AutoTest3' . date('YmdHis');
        $account->email = 'autotest3' . time() . '@example.org';
        $account->password = md5('password');
        $account->role = Users_Model_User::ROLE_USER;
        $account->status = Users_Model_User::STATUS_ACTIVE;
        $account->id = $userId;
        $account->save();

        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
        $subscription = $subscriptionManager->createSubscription($userId, $planId, $expirationDate);
        //Test create subscription
        $this->assertNotEmpty($subscription);

        //Test expired subscription
        $subscriptions = $subscriptionManager->getExpiredActiveSubscriptions();
        $this->assertNotEmpty($subscriptions);
        $this->assertCount(1, $subscriptions);

        //Change status
        $subscriptionManager->setInactiveStatusExpiredSubscriptions();

        //Test none expired subscription
        $subscriptions = $subscriptionManager->getExpiredActiveSubscriptions();
        $this->assertNotEmpty($subscriptions);
        $this->assertCount(0, $subscriptions);
    }

}
