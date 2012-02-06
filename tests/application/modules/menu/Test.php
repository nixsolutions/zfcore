<?php

class Menu_Test extends PHPUnit_Framework_TestSuite
{


    public static function suite()
    {
        require_once 'ManagementControllerTest.php';
        require_once 'models/Menu/ManagerTest.php';

        $suite = new self('Menu');
        $suite->addTest(new PHPUnit_Framework_TestSuite('Menu_ManagementControllerTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Menu_Model_Menu_ManagerTest'));

        return $suite;
    }

    protected function setUp()
    {
        ControllerTestCase::migrationUp('menu');
    }

    protected function tearDown()
    {
        ControllerTestCase::migrationDown('menu');
    }
}
