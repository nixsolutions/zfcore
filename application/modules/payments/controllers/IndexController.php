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
     * @throws Zend_Controller_Action_Exception
     */
    public function indexAction()
    {
        /**
         * merchant@zfcore.naxel.rhino.nixsolutions.com
         *
         * https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_profile-website-payments
         * https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_profile-summary
         *
         */
        $orderId = (int)$this->_getParam('orderId');

        if ($orderId) {
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

                $this->view->order = $order;
            } else {
                throw new Zend_Controller_Action_Exception('Page not found');
            }

        } else {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

    }


    public function completeAction()
    {
        $this->_helper->flashMessenger('Payment complete! Please wait a minute.');
        $this->_helper->redirector->gotoUrl('/subscriptions');
    }


    public function canceledAction()
    {
        $this->_helper->flashMessenger('Payment canceled :(');
        $this->_helper->redirector->gotoUrl('/subscriptions');
    }

}
