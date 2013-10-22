<?php
/**
 * Class Payments_PaypalController
 */
class Payments_PaypalController extends Core_Controller_Action
{

    /**
     * @var null|array
     */
    protected $_paypalConfig = null;


    public function init()
    {
        $paypalConfig = false;
        if (Zend_Registry::isRegistered('payments')) {
            $payments = Zend_Registry::get('payments');
            if (isset($payments['paypal']) && $payments['paypal']) {



                $paypalConfig = $payments['paypal'];
            }
        }

        if (!$paypalConfig) {
            throw new Exception($this->__("Paypal is not configured."));
        }

        $this->_paypalConfig = $paypalConfig;
    }


    /**
     * POST request from PayPal
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function callbackAction()
    {
        //check request
        if ($this->getRequest()->isPost()) {

            $txnId = $this->getRequest()->getParam('txn_id');
            $txnType = $this->getRequest()->getParam('txn_type');

            if ($txnId || ($txnType && $txnType === 'subscr_cancel')) {
                $params = $this->getRequest()->getParams();
                //check POST data
                if ($this->isCorrectPostParams($params)) {

                    //Format of custom field
                    //{'type'}-{$orderId}-{$userId}-{$planId}
                    $customParam = $this->getRequest()->getParam('custom');
                    $subscrId = $this->getRequest()->getParam('subscr_id');
                    $amount = $this->getRequest()->getParam('mc_gross');

                    list($orderType, $orderId, $userId) = explode('-', $customParam);

                    if ($orderType && $orderId && $userId) {
                        $orderModel = new Payments_Model_Order_Manager();

                        if ($txnType === 'subscr_cancel' && $subscrId) {
                            //Cancel subscription
                            if ($orderType === Payments_Model_Order::ORDER_TYPE_SUBSCRIPTION) {
                                $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
                                $subscriptionManager->cancelSubscriptionByPaypalCustomParam($customParam, $subscrId);
                                exit('ok');
                            } else {
                                //TBD
                                //For other types of order.
                            }

                        } elseif ($amount && $txnId) {
                            //Create order
                            if ($orderModel->payOrder($orderId, $userId, $amount, $txnId)) {
                                if ($orderType === Payments_Model_Order::ORDER_TYPE_SUBSCRIPTION) {
                                    $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
                                    $subscriptionManager->createSubscriptionByPaypalCustomParam($customParam, $subscrId);
                                    exit('ok');
                                } else {
                                    //TBD
                                    //For other types of order.
                                }
                            } else {
                                //Error!
                                //Order has been payed or incorrect data in custom field.
                            }
                        }

                    } else {
                        //Error!
                        //Incorrect data in custom field.
                    }
                } else {
                    //Error!
                    //Incorrect data in POST request
                }
            }
        }

        throw new Zend_Controller_Action_Exception('Page not found');
    }


    /**
     * Validate POST data
     *
     * @param array $params
     * @return bool
     */
    public function isCorrectPostParams($params)
    {
        $client = new Zend_Http_Client();
        $client->setMethod('POST');

        foreach ($params as $name => $value) {
            $client->setParameterPost($name, $value);
        }

        $client->setParameterPost('cmd', '_notify-validate');
        $response = $client->setUri($this->_paypalConfig['paypalHost'] . 'cgi-bin/webscr')->request();

        if ($response->getBody() == 'VERIFIED') {
            exit('VERIFIED');
            return true;
        }else{
            exit('not-VERIFIED');
        }
        return false;
    }


}
