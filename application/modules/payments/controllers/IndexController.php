<?php
/**
 * Payments_IndexController
 *
 * @category   Application
 * @package    Pages
 * @subpackage Controller
 */
class Payments_IndexController extends Core_Controller_Action
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
            if (isset($payments['gateways']) && $payments['gateways']
                && isset($payments['gateways']['paypal']) && $payments['gateways']['paypal']) {
                $paypalConfig = $payments['gateways']['paypal'];
            }
        }

        if (!$paypalConfig) {
            if (Zend_Registry::isRegistered('Log')) {
                $log = Zend_Registry::get('Log');
                $log->log("PayPal is not configured.", Zend_Log::CRIT);
            }
            throw new Exception($this->__("Paypal is not configured."));
        }

        $this->_paypalConfig = $paypalConfig;
    }


    /**
     * This action need call only from view
     *
     * Example:
         echo $this->action(
             'create',
             'index',
             'payments',
             array(
                 'orderId' => $this->orderId,
                 ...
                 'callFrom' => 'view'
             )
         );
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function createAction()
    {
        $orderId = (int)$this->_getParam('orderId');

        if ($orderId && $this->_getParam('callFrom') == 'view') {
            $ordersTable = new Payments_Model_Order_Table();
            $order = $ordersTable->getById($orderId);
            if ($order) {
                //Order info
                $this->view->title = $this->_getParam('title');
                $this->view->description = $this->_getParam('description');
                $this->view->orderId = $orderId;
                $this->view->price = $this->_getParam('price');
                $this->view->type = $this->_getParam('type');
                $this->view->paypalCustom = $this->_getParam('paypalCustom');

                // PayPal data
                $this->view->paypalHost = $this->_paypalConfig['paypalHost'];
                $this->view->paypalEmail = $this->_paypalConfig['email'];
                $this->view->paypalCurrency = $this->_paypalConfig['currency'];

                //Callbacks
                $this->view->completeCallback = $this->_getParam('return');
                $this->view->cancelCallback = $this->_getParam('cancel_return');

                $this->view->order = $order;
            } else {
                throw new Zend_Controller_Action_Exception('Page not found');
            }

        } else {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

    }

}
