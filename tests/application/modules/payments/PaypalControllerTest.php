<?php

class Payments_PaypalControllerTest extends ControllerTestCase
{

    /**
     * @expectedException Exception
     */
    public function testEmptyConfig()
    {
        Zend_Registry::set('payments', null);
        $orderManager = new Payments_Model_Order_Manager();
        $this->assertTrue($orderManager->handlePaypalRequest(array()));
    }


    public function testCallbackAction()
    {
        //GET (method is not allowed)
        $this->dispatch('/payments/paypal/callback');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains('<b>Message:</b> Page not found</p>', $this->getResponse()->getBody());
    }


    public function testCallbackActionIncorrectTxnId()
    {
        //POST (incorrect txn_id)
        $this->request
            ->setMethod('POST')
            ->setPost(array('txn_id' => 123));
        $this->dispatch('/payments/paypal/callback');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains('<b>Message:</b> Page not found</p>', $this->getResponse()->getBody());
    }


    public function testCallbackActionCorrectTxnId()
    {
        //Fake data
        $orderType = Subscriptions_Model_Subscription::ORDER_TYPE_SUBSCRIPTION;
        $planId = '3';
        $price = '50';

        //Create user
        $account = new Users_Model_User();
        $account->avatar = null;
        $account->login = 'testCallbackActionCorrectTxnId' . date('YmdHis');
        $account->email = 'testCallbackActionCorrectTxnId' . time() . '@example.org';
        $account->password = md5('password');
        $account->role = Users_Model_User::ROLE_USER;
        $account->status = Users_Model_User::STATUS_ACTIVE;
        $account->save();

        //Create order
        $orderManager = new Payments_Model_Order_Manager();
        $order = $orderManager->createOrder($account->id, $price);

        $mock = $this->getMock('Payments_Model_Order_Manager', array('checkPaypalPostParams'));
        $mock->expects($this->any())
            ->method('checkPaypalPostParams')
            ->will($this->returnValue(true));
        $mock->setDbTable('Payments_Model_Order_Table');

        //Create custom param
        $customParam = implode('-', array($orderType, $order->id));

        $params = array(
            'custom' => '111',
            'subscr_id' => 111,
            'mc_gross' => 0,
            'txn_type' => 111,
            'txn_id' => 111
        );

        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
        $subscriptionManager->createPaidSubscription($account->id, $planId, $order->id);

        //Test incorrect custom param
        try {
            $mock->handlePaypalRequest($params);
            $this->fail('An expected exception has not been raised.');
        } catch (Exception $ex) {
            // Test ok.
        }

        //Test incorrect custom param
        $params['custom'] = '0-0';
        try {
            $mock->handlePaypalRequest($params);
            $this->fail('An expected exception has not been raised.');
        } catch (Exception $ex) {
            // Test ok.
        }

        $params['custom'] = $customParam;
        try {
            $mock->handlePaypalRequest($params);
            $this->fail('An expected exception has not been raised.');
        } catch (Exception $ex) {
            // Test empty $amount.
        }

        $params['mc_gross'] = $price;
        $this->assertTrue($mock->handlePaypalRequest($params));

        //Test payOrder() incorrect orderType
        $params['custom'] = implode('-', array('incorrect', $order->id));
        try {
            $mock->handlePaypalRequest($params);
            $this->fail('An expected exception has not been raised.');
        } catch (Exception $ex) {
            // Test ok.
        }

        //Test payOrder() incorrect orderId
        $params['custom'] = implode('-', array('incorrect', '12345678'));
        try {
            $mock->handlePaypalRequest($params);
            $this->fail('An expected exception has not been raised.');
        } catch (Exception $ex) {
            // Test ok.
        }

        //Test Cancel Subscription
        $params['txn_type'] = 'subscr_cancel';
        $params['custom'] = $customParam;
        $this->assertTrue($mock->handlePaypalRequest($params));

        //Test Cancel Subscription incorrect orderType
        $params['custom'] = implode('-', array('incorrect', $order->id));
        try {
            $mock->handlePaypalRequest($params);
            $this->fail('An expected exception has not been raised.');
        } catch (Exception $ex) {
            // Test ok.
        }
    }

}
