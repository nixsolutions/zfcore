<?php
class Payments_Test extends Core_Tests_PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';
        require_once 'PaypalControllerTest.php';
        require_once 'models/Order/ManagerTest.php';

        $suite = new self('Payments');

        $testClasses = array(
            'Payments_IndexControllerTest',
            'Payments_PaypalControllerTest',
            'Payments_Model_Order_ManagerTest'
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
