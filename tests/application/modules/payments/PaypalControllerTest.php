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
        $this->assertTrue($orderManager->validateAndPayOrder(array()));
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
        $orderType = Payments_Model_Order::ORDER_TYPE_SUBSCRIPTION;
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

        //Create custom param
        $customParam = implode('-', array($orderType, $order->id, $account->id, $planId));

        $mock = $this->getMock('Payments_Model_Order_Manager', array('isCorrectPostParams'));

        $mock->expects($this->any())
            ->method('isCorrectPostParams')
            ->will($this->returnValue(true));
        $mock->setDbTable('Payments_Model_Order_Table');

        $params = array(
            'custom' => $customParam,
            'subscr_id' => 111,
            'mc_gross' => $price,
            'txn_type' => 111,
            'txn_id' => 111
        );

        $this->assertTrue($mock->validateAndPayOrder($params));

        $params['txn_type'] = 'subscr_cancel';
        $this->assertTrue($mock->validateAndPayOrder($params));
    }
}
