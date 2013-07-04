<?php
class Translate_Test extends Core_Tests_PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'ManagementControllerTest.php';

        $suite = new self('Translate');

        $testClasses = array(
            'Translate_ManagementControllerTest'
        );

        $suite->addTests($testClasses);

        return $suite;
    }


    protected function setUp()
    {
        if ($this->count()) {
            ControllerTestCase::migrationUp('translate');
        }
    }

    protected function tearDown()
    {
        if ($this->count()) {
            ControllerTestCase::migrationDown('translate');
        }
    }

}
