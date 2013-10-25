<?php
class Payments_Model_Order_Manager extends Core_Model_Manager
{

    /**
     * Create order
     *
     * @param int $userId
     * @param float $amount
     * @return Payments_Model_Order
     */
    public function createOrder($userId, $amount)
    {
        $order = $this->getDbTable()->createRow();
        $order->status = Payments_Model_Order::ORDER_STATUS_WAITING;
        $order->created = date('Y-m-d H:i:s');
        $order->userId = $userId;
        $order->amount = $amount;
        $order->save();
        return $order;
    }


    /**
     * @param int $orderId
     * @param int $userId
     * @param float $amount
     * @param string $txnId
     * @return bool
     */
    public function payOrder($orderId, $userId, $amount, $txnId)
    {
        $select = $this->getDbTable()
            ->select()
            ->where('id = ?', $orderId)
            ->where('userId = ?', $userId)
            ->where('amount = ?', $amount)
            ->where('status = ?', Payments_Model_Order::ORDER_STATUS_WAITING);
        $order = $this->getDbTable()->fetchRow($select);
        if ($order) {
            $order->status = Payments_Model_Order::ORDER_STATUS_COMPLETE;
            $order->transactionId = $txnId;
            $order->paidDate = date('Y-m-d H:i:s');
            $order->save();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $params
     * @return bool
     * @throws Exception
     */
    public function handlePaypalRequest($params)
    {
        $paypalConfig = false;
        if (Zend_Registry::isRegistered('payments')) {
            $payments = Zend_Registry::get('payments');
            if (isset($payments['paypal']) && $payments['paypal']) {
                $paypalConfig = $payments['paypal'];
            }
        }

        if (!$paypalConfig) {
            throw new Exception("PayPal is not configured.");
        }

        //check POST data
        $this->checkPaypalPostParams($params, $paypalConfig);
        //Format of custom field
        //{'type'}-{$orderId}-{$userId}-{$planId}
        $customParam = $params['custom'];
        $subscrId = $params['subscr_id'];
        $amount = $params['mc_gross'];
        $txnType = $params['txn_type'];
        $txnId = $params['txn_id'];

        $customParamArray = explode('-', $customParam);
        if (count($customParamArray) !== 4) {
            throw new Exception("Incorrect format in PayPal custom param: " . var_export($customParamArray, true));
        }

        list($orderType, $orderId, $userId) = $customParamArray;

        if (!$orderType || !$orderId || !$userId) {
            throw new Exception("Incorrect data in PayPal custom param: " . var_export($customParam, true));
        }

        if ($orderType === Payments_Model_Order::ORDER_TYPE_SUBSCRIPTION) {

            if ($txnType === 'subscr_cancel' && $subscrId) {
                //Cancel subscription
                $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
                return $subscriptionManager->cancelSubscriptionByPaypalCustomParam($customParam, $subscrId);

            } else {

                if (!$amount || !$txnId) {
                    throw new Exception('Incorrect data from PayPal. $amount = ' . $amount . ' $txnId = ' . $txnId);
                }
                //Create order
                if ($this->payOrder($orderId, $userId, $amount, $txnId)) {
                    $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
                    return $subscriptionManager->createSubscriptionByPaypalCustomParam($customParam, $subscrId);
                } else {
                    //Error! Order has been payed or incorrect data in custom field.
                    throw new Exception('Order has been payed or incorrect data in custom field. Params: $orderId = '
                        . $orderId . ', $userId = ' . $userId . ', $amount = ' . $amount . ', $txnId = '
                        . $txnId . '.');
                }
            }

        } else {
            //TBD. For other types of order.
            throw new Exception('Incorrect $orderType: ' . $orderType);
        }

    }


    /**
     * Validate POST data
     *
     * @param array $params
     * @param array $paypalConfig
     * @throws Exception
     */
    public function checkPaypalPostParams($params, $paypalConfig)
    {
        $client = new Zend_Http_Client();
        $client->setMethod('POST');

        foreach ($params as $name => $value) {
            $client->setParameterPost($name, $value);
        }

        $client->setParameterPost('cmd', '_notify-validate');
        $response = $client->setUri($paypalConfig['paypalHost'] . 'cgi-bin/webscr')->request();

        if ($response->getBody() !== 'VERIFIED') {
            throw new Exception("PayPal returned: " . $response->getBody());
        }
    }

}
