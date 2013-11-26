<?php
/**
 * Class Payments_Model_Order_ManagerTest
 */
class Payments_Model_Order_ManagerTest extends ControllerTestCase
{


    public function testCreateOrder()
    {
        //Fake data
        $userId = '123456789';
        $amount = '50';
        $txnId = 'test-txn-id';

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

        $orderManager = new Payments_Model_Order_Manager();
        $order = $orderManager->createOrder($userId, $amount);
        //Is created order
        $this->assertNotEmpty($order);

        $response = $orderManager->payOrder(
            $order->id,
            $txnId,
            Payments_Model_Order::PAYMENT_SYSTEM_PAYPAL
        );
        //Is payed order
        $this->assertTrue($response);

        //Incorrect orderId
        $response = $orderManager->payOrder(
            '123456789',
            $txnId,
            Payments_Model_Order::PAYMENT_SYSTEM_PAYPAL
        );
        $this->assertFalse($response);
    }

}
