<?php

class Subscriptions_Model_Subscription_Manager extends Core_Model_Manager
{

    /***
     * Create or renewal subscription
     *
     * @param int $userId
     * @param int $planId
     * @param null|string $expirationDate If null - unlimited subscription
     * @param int|null $orderId If null - free subscription
     * @param int|null $subscrId Null for one-time payment and string for PayPal subscription
     * @return Zend_Db_Table_Row_Abstract
     */
    public function createSubscription($userId, $planId, $expirationDate = null, $orderId = null, $subscrId = null)
    {
        $subscription = null;
        if ($subscrId) {
            //Get subscription if exist
            $select = $this->getDbTable()->select()
                ->where('userId =?', $userId)
                ->where('orderId =?', $orderId)
                ->where('subscriptionPlanId =?', $planId)
                ->where('paypalSubscrId =?', $subscrId);
            $subscription = $this->getDbTable()->fetchRow($select);
        }

        if ($subscription) {
            //For renewal subscriptions
            $subscription->updated = date('Y-m-d H:i:s');
        } else {
            //Create new subscription
            $subscription = $this->getDbTable()->createRow();
            $subscription->created = date('Y-m-d H:i:s');
            $subscription->subscriptionPlanId = $planId;
            //If Null $orderId - free subscription
            $subscription->orderId = $orderId;
            $subscription->userId = $userId;
        }

        if ($subscrId) {
            //Add PayPal subscriptionId if it is recurrent payment
            $subscription->paypalSubscrId = $subscrId;
        }

        //If Null $expirationDate - infinite
        $subscription->expirationDate = $expirationDate;
        $subscription->status = Subscriptions_Model_Subscription::STATUS_ACTIVE;

        $subscription->save();
        return $subscription;
    }


    /**
     * @param int $userId
     */
    public function disableAllSubscriptionsByUserId($userId)
    {
        $this->getDbTable()->update(
            array(
                'status' => Subscriptions_Model_Subscription::STATUS_INACTIVE
            ),
            'userId = "' . $userId . '"'
        );
    }


    /**
     * Cancel PayPal subscription
     *
     * @param int $userId
     * @param int $planId
     * @param int $orderId
     * @param string $subscrId
     * @return bool
     */
    public function cancelSubscription($userId, $planId, $orderId, $subscrId)
    {
        //Get subscription if exist
        $select = $this->getDbTable()->select()
            ->where('userId =?', $userId)
            ->where('orderId =?', $orderId)
            ->where('subscriptionPlanId =?', $planId)
            ->where('status =?', Subscriptions_Model_Subscription::STATUS_ACTIVE)
            ->where('paypalSubscrId =?', $subscrId);
        $subscription = $this->getDbTable()->fetchRow($select);

        if ($subscription) {
            $subscription->updated = date('Y-m-d H:i:s');
            $subscription->status = Subscriptions_Model_Subscription::STATUS_CANCELED;
            $subscription->save();
            return true;
        }
        return false;
    }


    /**
     * Get inactive subscriptions
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getExpiredActiveSubscriptions()
    {
        $select = $this->getDbTable()->select()
            ->setIntegrityCheck(false)
            ->from(array('s' => 'subscriptions'), array('*'))
            ->joinLeft('users', 'users.id = s.userId', array('login', 'email'))
            ->where('s.status =?', Subscriptions_Model_Subscription::STATUS_ACTIVE)
            ->where('s.expirationDate <?', date('Y-m-d H:i:s'));
        return $this->getDbTable()->fetchAll($select);
    }


    /**
     * Set inactive status to expired subscriptions
     */
    public function setInactiveStatusExpiredSubscriptions()
    {
        $this->getDbTable()->update(
            array(
                'status' => Subscriptions_Model_Subscription::STATUS_INACTIVE
            ),
            'expirationDate < "' . date('Y-m-d H:i:s') . '"'
        );
    }


    /**
     * Get expiration date
     * If null - plan with unlimited period
     *
     * @param $userId
     * @param $planId
     * @return bool|null|string
     */
    public function getExpirationDate($userId, $planId)
    {
        //Get subscription plan
        $subscriptionPlansTable = new Subscriptions_Model_SubscriptionPlans_Table();
        $subscriptionPlan = $subscriptionPlansTable->getById($planId);

        //If plan have period
        if ($subscriptionPlan->period > 0) {
            $select = $this->getDbTable()->select()
                ->from(array('subscriptions'), array('expirationDate'))
                ->where('userId =?', $userId)
                ->where('subscriptionPlanId =?', $planId)
                ->where('status =?', Subscriptions_Model_Subscription::STATUS_ACTIVE)
                ->where('expirationDate > ?', date('Y-m-d H:i:s'))
                ->order('expirationDate DESC');
            $subscriptionRow = $this->getDbTable()->fetchRow($select);

            if ($subscriptionRow && $subscriptionRow->expirationDate) {
                $seconds = (int)$subscriptionPlan->period * 86400;
                return $expirationDate = date('Y-m-d H:i:s', strtotime($subscriptionRow->expirationDate) + $seconds);
            } else {
                //If is first subscription this type
                return $expirationDate = date(
                    'Y-m-d H:i:s',
                    mktime(date("H"), date("i"), date("s"), date("m"), (date("d") + (int)$subscriptionPlan->period), date("Y"))
                );
            }
        }

        //Plan with unlimited period
        return null;
    }


    /**
     * @param int $userId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function getCurrentSubscription($userId)
    {
        //@todo fix it
        $select = $this->getDbTable()->select()
            ->from(array('subscriptions'))
            ->where('status =?', Subscriptions_Model_Subscription::STATUS_ACTIVE)
            ->where('userId =?', $userId)
            ->order('created DESC');
        return $this->getDbTable()->fetchRow($select);
    }


    /**
     * Create subscription by PayPal custom field in request
     *
     * @param string $customParam
     * @param string|null $subscrId
     * @return Zend_Db_Table_Row_Abstract
     */
    public function createSubscriptionByPaypalCustomParam($customParam, $subscrId = null)
    {
        list($orderType, $orderId, $userId, $planId) = explode('-', $customParam);

        //Create expiration date
        $expirationDate = $this->getExpirationDate($userId, $planId);

        //Disable old subscriptions
        $this->disableAllSubscriptionsByUserId($userId);

        //Create subscription
        if ($this->createSubscription($userId, $planId, $expirationDate, $orderId, $subscrId)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Disable subscriptions
     *
     * @param array $customParam
     * @param string $subscrId
     * @return bool
     */
    public function cancelSubscriptionByPaypalCustomParam($customParam, $subscrId)
    {
        list($orderType, $orderId, $userId, $planId) = explode('-', $customParam);

        //Disable old subscriptions
        return $this->cancelSubscription($userId, $planId, $orderId, $subscrId);
    }

}
