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
        $orderType = Subscriptions_Model_Subscription::ORDER_TYPE_SUBSCRIPTION;
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
        $account->save();

        //Create order
        $orderManager = new Payments_Model_Order_Manager();
        $order = $orderManager->createOrder($account->id, $price);


        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
        $this->assertNotNull($subscriptionManager->createPaidSubscription($account->id, $planId, $order->id));

        //Test paySubscription incorrect orderId
        $this->assertFalse($subscriptionManager->paySubscription('123456789'));

        //Test paySubscription
        $this->assertTrue($subscriptionManager->paySubscription($order->id));

        //Test canceled subscription
        $this->assertTrue($subscriptionManager->cancelSubscription($order->id));

        //Test canceled subscription incorrect orderId
        $this->assertFalse($subscriptionManager->cancelSubscription('123456789'));
    }


    public function testCreateSubscription()
    {
        //Fake data
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
        $account->save();

        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
        $subscription = $subscriptionManager->createFreeSubscription($account->id, $planId);
        //Test create subscription
        $this->assertNotEmpty($subscription);

        //Test expired subscription
        $subscription->expirationDate = $expirationDate;
        $subscription->save();

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


    public function testGetExpirationDate()
    {
        //Create user
        $account = new Users_Model_User();
        $account->avatar = null;
        $account->login = 'testGetExpirationDate' . date('YmdHis');
        $account->email = 'testGetExpirationDate' . time() . '@example.org';
        $account->password = md5('password');
        $account->role = Users_Model_User::ROLE_USER;
        $account->status = Users_Model_User::STATUS_ACTIVE;
        $account->save();

        //Get subscription plan
        $subscriptionPlansTable = new Subscriptions_Model_SubscriptionPlans_Table();
        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();

        //Get plan with infinite subscription
        $subscriptionPlan = $subscriptionPlansTable->getByType(Subscriptions_Model_SubscriptionPlan::PLAN_TYPE_INFINITE);
        $expirationDate = $subscriptionManager->getExpirationDate($account->id, $subscriptionPlan->id);
        $this->assertNull($expirationDate);

        //Get plan with monthly subscription
        $subscriptionPlan = $subscriptionPlansTable->getByType(Subscriptions_Model_SubscriptionPlan::PLAN_TYPE_MONTHLY);
        $expirationDate = $subscriptionManager->getExpirationDate($account->id, $subscriptionPlan->id);
        $this->assertEquals(
            date('Y-m-d H:i:s',mktime(date("H"), date("i"), date("s"), date("m")  , date("d")+30, date("Y"))),
            $expirationDate
        );

    }

}
