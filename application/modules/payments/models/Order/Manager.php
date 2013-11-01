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
     * @param string $txnId
     * @param string $paymentSystem
     * @param string|null $paymentSubscrId
     * @return bool
     */
    public function payOrder($orderId, $txnId, $paymentSystem, $paymentSubscrId = null)
    {
        $select = $this->getDbTable()
            ->select()
            ->where('id = ?', $orderId);
        $order = $this->getDbTable()->fetchRow($select);
        if ($order) {
            $order->status = Payments_Model_Order::ORDER_STATUS_COMPLETE;
            $order->transactionId = $txnId;
            $order->paidDate = date('Y-m-d H:i:s');
            $order->paymentSystem = $paymentSystem;
            $order->paymentSubscrId = $paymentSubscrId;
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
            if (isset($payments['gateways']) && $payments['gateways']
                && isset($payments['gateways']['paypal']) && $payments['gateways']['paypal']
            ) {
                $paypalConfig = $payments['gateways']['paypal'];
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
        if (count($customParamArray) !== 2) {
            throw new Exception("Incorrect format in PayPal custom param: " . var_export($customParamArray, true));
        }

        list($orderType, $orderId) = $customParamArray;

        if (!$orderType || !$orderId) {
            throw new Exception("Incorrect data in PayPal custom param: " . var_export($customParam, true));
        }

        if ($txnType === 'subscr_cancel') {
            //Cancel subscription
            if (isset($payments['events']) && isset($payments['events']['cancel' . ucfirst($orderType)])) {
                //Triggering event
                return call_user_func(
                    array(
                        new $payments['events']['cancel' . ucfirst($orderType)]['class'],
                        $payments['events']['cancel' . ucfirst($orderType)]['method']
                    ),
                    $orderId
                );
            }

        } else {

            if (!$amount || !$txnId) {
                throw new Exception('Incorrect data from PayPal. $amount = ' . $amount . ' $txnId = ' . $txnId);
            }

            //Pay order
            if ($this->payOrder($orderId, $txnId, Payments_Model_Order::PAYMENT_SYSTEM_PAYPAL, $subscrId)) {
                if (isset($payments['events']) && isset($payments['events']['pay' . ucfirst($orderType)])) {
                    //Triggering event
                    //paySubscription($userId, $orderId)
                    return call_user_func(
                        array(
                            new $payments['events']['pay' . ucfirst($orderType)]['class'],
                            $payments['events']['pay' . ucfirst($orderType)]['method']
                        ),
                        $orderId
                    );
                } else {
                    throw new Exception('Incorrect $orderType: ' . $orderType);
                }
            } else {
                //Error! Order has been payed or incorrect data in custom field.
                throw new Exception('Order has been payed or incorrect data in custom field. Params: $orderId = '
                    . $orderId . ', $amount = ' . $amount . ', $txnId = '
                    . $txnId . '.');
            }
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
