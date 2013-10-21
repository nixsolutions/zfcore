<?php
/**
 * User: naxel
 * Date: 30.07.13 16:33
 */

class Subscriptions_Model_SubscriptionPlan extends Core_Db_Table_Row_Abstract
{
    /** Plan types */
    const PLAN_TYPE_TRIAL  = 'trial';
    const PLAN_TYPE_FREE  = 'free';
    const PLAN_TYPE_MONTHLY  = 'monthly';
    const PLAN_TYPE_INFINITE = 'infinite';

}