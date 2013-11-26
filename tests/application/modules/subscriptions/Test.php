<?php
class Subscriptions_Test extends Core_Tests_PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';
        require_once 'models/Subscription/ManagerTest.php';

        $suite = new self('Subscriptions');

        $testClasses = array(
            'Subscriptions_IndexControllerTest',
            'Subscriptions_Model_Subscription_ManagerTest'
        );

        $suite->addTests($testClasses);


        return $suite;
    }


    protected function setUp()
    {
        if ($this->count()) {
            ControllerTestCase::migrationUp('payments');
            ControllerTestCase::migrationUp('subscriptions');
        }
    }

    protected function tearDown()
    {
        if ($this->count()) {
            ControllerTestCase::migrationDown('subscriptions');
            ControllerTestCase::migrationDown('payments');
        }
    }

}
