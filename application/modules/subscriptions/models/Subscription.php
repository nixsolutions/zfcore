<?php
/**
 * User: naxel
 * Date: 01.08.13 12:25
 */

class Subscriptions_Model_Subscription extends Core_Db_Table_Row_Abstract
{

    const STATUS_INITIAL = 'initial'; //Only for paid subscriptions. Changed to active after paid.
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_CANCELED = 'canceled';

    const ORDER_TYPE_SUBSCRIPTION = 'subscription';

}
