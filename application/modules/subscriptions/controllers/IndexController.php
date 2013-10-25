<?php
/**
 * IndexController
 *
 * @category   Application
 * @package    Subscriptions
 * @subpackage Controller
 */
class Subscriptions_IndexController extends Core_Controller_Action
{

    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    private $_flashMessenger;

    public function init()
    {
        /* Initialize action controller here */
        parent::init();
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }


    /**
     * List of subscriptions
     */
    public function indexAction()
    {
        $subscriptionPlansTable = new Subscriptions_Model_SubscriptionPlans_Table();
        $this->view->subscriptionPlan = $subscriptionPlansTable->fetchAll();

        //Get user
        $identity = Zend_Auth::getInstance()->getIdentity();

        if ($identity) {
            $userId = $identity->id;

            //Get current subscription
            $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
            $currentSubscription = $subscriptionManager->getCurrentSubscription($userId);

            if ($currentSubscription) {
                $subscriptionPlansTable = new Subscriptions_Model_SubscriptionPlans_Table();
                $currentSubscriptionPlan = $subscriptionPlansTable->getById($currentSubscription->subscriptionPlanId);

                $paypalHost = false;
                if (Zend_Registry::isRegistered('payments')) {
                    $payments = Zend_Registry::get('payments');
                    if (isset($payments['paypal']) && $payments['paypal']) {
                        $paypalHost = $payments['paypal']['paypalHost'];
                    }
                }

                if (!$paypalHost) {
                    if (Zend_Registry::isRegistered('Log')) {
                        $log = Zend_Registry::get('Log');
                        $log->log("PayPal is not configured.", Zend_Log::CRIT);
                    }
                    throw new Exception($this->__("Paypal is not configured."));
                }

                $this->view->paypalHost = $paypalHost;

                $this->view->currentSubscription = $currentSubscription;
                $this->view->currentSubscriptionPlan = $currentSubscriptionPlan;
            }
        }
    }


    /**
     * Create subscription page
     *
     * If selected free or trial plan - changes immediately
     * If selected paid plan user allow select payment method (PayPAl or another payment system) and pay subscription
     */
    public function createAction()
    {
        $planId = $this->_getParam('id');
        if ($this->_request->isPost() && $planId) {

            //Get user
            $identity = Zend_Auth::getInstance()->getIdentity();
            $userId = $identity->id;

            //Get plan
            $subscriptionPlansTable = new Subscriptions_Model_SubscriptionPlans_Table();
            $subscriptionPlan = $subscriptionPlansTable->getById($planId);

            //Not free subscription
            if ($subscriptionPlan->price > 0) {
                //Create order
                $orderManager = new Payments_Model_Order_Manager();
                $order = $orderManager->createOrder($userId, $subscriptionPlan->price);

                $this->view->orderId = $order->id;
                $this->view->subscriptionPlan = $subscriptionPlan;

                //'type'-orderId-userId-planId
                $this->view->paypalCustom = Payments_Model_Order::ORDER_TYPE_SUBSCRIPTION . '-' . $order->id . '-' . $userId . '-' . $subscriptionPlan->id;

            } else {
                //Free subscription
                try {
                    //Not allow renewal Trial subscription
                    if ($subscriptionPlan->type === Subscriptions_Model_SubscriptionPlan::PLAN_TYPE_TRIAL) {

                        $subscriptionTable = new Subscriptions_Model_Subscription_Table();
                        $select = $subscriptionTable->select()
                            ->from(array('subscriptions'))
                            ->where('userId =?', $userId)
                            ->where('subscriptionPlanId = ?', $subscriptionPlan->id);
                        if ($subscriptionTable->fetchRow($select)) {
                            //Found old trial plan
                            throw new Zend_Controller_Action_Exception('You can not change to this plan');
                        }

                    }

                    //Create expiration date
                    $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
                    $expirationDate = $subscriptionManager->getExpirationDate($userId, $planId);

                    $subscriptionManger = new Subscriptions_Model_Subscription_Manager();

                    //Disable old subscriptions
                    $subscriptionManger->disableAllSubscriptionsByUserId($userId);

                    //Create subscription
                    $subscriptionManger->createSubscription($userId, $planId, $expirationDate);

                    //Redirect to Thank you page
                    $this->redirect('/subscriptions/index/complete');
                } catch (Zend_Controller_Action_Exception $ex) {
                    $this->_flashMessenger->addMessage($ex->getMessage());
                    $this->redirect('/subscriptions');
                }

            }
        } else {
            $this->redirect('/subscriptions');
        }
    }


    /**
     * Page which info selected subscription.
     * Only for free subscriptions type.
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function completeAction()
    {
        //Get user
        $identity = Zend_Auth::getInstance()->getIdentity();
        $userId = $identity->id;
        //Get current subscription
        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
        $currentSubscription = $subscriptionManager->getCurrentSubscription($userId);

        if ($currentSubscription) {
            $subscriptionPlansTable = new Subscriptions_Model_SubscriptionPlans_Table();
            $subscriptionPlan = $subscriptionPlansTable->getById($currentSubscription->subscriptionPlanId);

            $this->view->currentSubscription = $currentSubscription;
            $this->view->subscriptionPlan = $subscriptionPlan;
        } else {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

    }

    /**
     * @throws Zend_Controller_Action_Exception
     */
    public function planInfoAction()
    {
        //Get user
        $identity = Zend_Auth::getInstance()->getIdentity();
        $userId = $identity->id;
        //Get current subscription
        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
        $currentSubscription = $subscriptionManager->getCurrentSubscription($userId);

        if ($currentSubscription) {
            $subscriptionPlansTable = new Subscriptions_Model_SubscriptionPlans_Table();
            $subscriptionPlan = $subscriptionPlansTable->getById($currentSubscription->subscriptionPlanId);

            $this->view->currentSubscription = $currentSubscription;
            $this->view->subscriptionPlan = $subscriptionPlan;
        } else {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

    }

}
