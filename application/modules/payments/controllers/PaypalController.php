<?php
/**
 * Class Payments_PaypalController
 */
class Payments_PaypalController extends Core_Controller_Action
{

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
                $orderManager = new Payments_Model_Order_Manager();
                try {

                    $orderManager->handlePaypalRequest($params);
                    if (Zend_Registry::isRegistered('Log')) {
                        $log = Zend_Registry::get('Log');
                        $log->log('Payment with params ' . var_export($params, true) . ' is successful.', Zend_Log::INFO);
                    }
                    return; //ok

                } catch (Exception $e) {
                    if (Zend_Registry::isRegistered('Log')) {
                        $log = Zend_Registry::get('Log');
                        $log->log($e->getMessage() . ' at ' . $e->getFile() . '#' . $e->getLine(), Zend_Log::CRIT);
                    }
                }
            }
        } else {
            if (Zend_Registry::isRegistered('Log')) {
                $log = Zend_Registry::get('Log');
                $log->log('GET request to Payments_PaypalController->callbackAction()', Zend_Log::WARN);
            }
        }

        throw new Zend_Controller_Action_Exception('Page not found');
    }
}
