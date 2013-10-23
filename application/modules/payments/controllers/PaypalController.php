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
                if ($orderManager->validateAndPayOrder($params)) {
                    exit('ok');
                }
            }
        }

        throw new Zend_Controller_Action_Exception('Page not found');
    }
}
