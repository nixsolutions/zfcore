<?php
/**
 * User: naxel
 * Date: 01.08.13 10:36
 */

class Payments_Model_Order extends Core_Db_Table_Row_Abstract
{

    //Order statuses
    const ORDER_STATUS_WAITING  = 'waiting';
    const ORDER_STATUS_COMPLETE  = 'complete';
    const ORDER_STATUS_CANCELED  = 'canceled';

    const ORDER_PERIODICITY_TYPE_ONETIME  = 'onetime';
    const ORDER_PERIODICITY_TYPE_MONTHLY  = 'monthly';


    const PAYMENT_SYSTEM_PAYPAL = 'paypal';

}
