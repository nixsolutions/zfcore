<?php

class Subscriptions_CliController extends Core_Controller_Action_Cli
{

    public function checkStatusAction()
    {
        // $ php public/index.php subscriptions cli check-status
        $this->printMessage('Check and set Inactive status to expired subscriptions', Core_Controller_Action_Cli::INFO_MESSAGE);
        $subscriptionManager = new Subscriptions_Model_Subscription_Manager();
        $subscriptionManager->setInactiveStatusExpiredSubscriptions();
    }
}
