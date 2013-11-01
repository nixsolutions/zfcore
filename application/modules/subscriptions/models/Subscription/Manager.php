<?php

class Subscriptions_Model_Subscription_Manager extends Core_Model_Manager
{

    /***
     * Create or renewal subscription
     *
     * @param int $userId
     * @param int $planId
     * @param int $orderId If null - free subscription
     * @return Zend_Db_Table_Row_Abstract
     */
    public function createPaidSubscription($userId, $planId, $orderId)
    {
        //Disable old subscriptions
        $this->disableAllSubscriptionsByUserId($userId);

        //Create new subscription
        $subscription = $this->getDbTable()->createRow();
        $subscription->created = date('Y-m-d H:i:s');
        $subscription->subscriptionPlanId = $planId;
        //If Null $orderId - free subscription
        $subscription->orderId = $orderId;
        $subscription->userId = $userId;

        $subscription->status = Subscriptions_Model_Subscription::STATUS_INITIAL;

        $subscription->save();
        return $subscription;
    }


    public function paySubscription($orderId)
    {
        $select = $this->getDbTable()->select()
            ->where('orderId =?', $orderId);
        $subscription = $this->getDbTable()->fetchRow($select);
        if (!$subscription) {
            return false;
        }

        $subscription->status = Subscriptions_Model_Subscription::STATUS_ACTIVE;
        $subscription->expirationDate = $this->getExpirationDate(
            $subscription->userId,
            $subscription->subscriptionPlanId
        );
        $subscription->updated = date('Y-m-d H:i:s');

        return (bool)$subscription->save();
    }


    /**
     * @param $userId
     * @param $planId
     * @return Zend_Db_Table_Row_Abstract
     */
    public function createFreeSubscription($userId, $planId)
    {
        //Disable old subscriptions
        $this->disableAllSubscriptionsByUserId($userId);

        //Create new subscription
        $subscription = $this->getDbTable()->createRow();
        $subscription->created = date('Y-m-d H:i:s');
        $subscription->subscriptionPlanId = $planId;
        $subscription->userId = $userId;

        //Create expiration date, If Null $expirationDate - infinite
        $subscription->expirationDate = $this->getExpirationDate($userId, $planId);
        $subscription->status = Subscriptions_Model_Subscription::STATUS_ACTIVE;
        $subscription->save();

        return $subscription;
    }


    /**
     * Cancel PayPal subscription
     *
     * @param int $orderId
     * @return bool
     */
    public function cancelSubscription($orderId)
    {
        //Get subscription if exist
        $select = $this->getDbTable()->select()
            ->where('orderId =?', $orderId);
        $subscription = $this->getDbTable()->fetchRow($select);

        if ($subscription) {
            $subscription->updated = date('Y-m-d H:i:s');
            $subscription->status = Subscriptions_Model_Subscription::STATUS_CANCELED;
            return (bool)$subscription->save();
        }
        return false;
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
        $select = $this->getDbTable()->select()
            ->setIntegrityCheck(false)
            ->from(array('s' => 'subscriptions'))
            ->joinLeft(array('o' => 'orders'), 'o.id = s.orderId', array('o.paymentSystem', 'o.paymentSubscrId'))
            ->where('s.status =?', Subscriptions_Model_Subscription::STATUS_ACTIVE)
            ->where('s.userId =?', $userId)
            ->order('s.created DESC');
        return $this->getDbTable()->fetchRow($select);
    }

}
