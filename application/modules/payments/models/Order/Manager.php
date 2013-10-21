<?php
class Payments_Model_Order_Manager extends Core_Model_Manager
{

    /**
     * Create order
     *
     * @param int $userId
     * @param float $amount
     * @return Payments_Model_Order
     */
    public function createOrder($userId, $amount)
    {
        $order = $this->getDbTable()->createRow();
        $order->status = Payments_Model_Order::ORDER_STATUS_WAITING;
        $order->created = date('Y-m-d H:i:s');
        $order->userId = $userId;
        $order->payment = $amount;
        $order->save();
        return $order;
    }


    /**
     * @param int $orderId
     * @param int $userId
     * @param float $amount
     * @param string $txnId
     * @return bool
     */
    public function payOrder($orderId, $userId, $amount, $txnId)
    {
        $select = $this->getDbTable()
            ->select()
            ->where('id = ?', $orderId)
            ->where('userId = ?', $userId)
            ->where('payment = ?', $amount)
            ->where('status = ?', Payments_Model_Order::ORDER_STATUS_WAITING);
        $order = $this->getDbTable()->fetchRow($select);
        if ($order) {
            $order->status = Payments_Model_Order::ORDER_STATUS_COMPLETE;
            $order->transactionId = $txnId;
            $order->paidDate = date('Y-m-d H:i:s');
            $order->save();
            return true;
        } else {
            return false;
        }
    }

}
