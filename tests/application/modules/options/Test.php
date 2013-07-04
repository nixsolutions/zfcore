<?php

class Options_Test extends Core_Tests_PHPUnit_Framework_TestSuite
{


    public static function suite()
    {
        require_once 'models/OptionTest.php';

        $suite = new self('Options');

        $testClasses = array(
            'Model_OptionTest'
        );

        $suite->addTests($testClasses);

        return $suite;
    }

    protected function setUp()
    {
        if ($this->count()) {
            ControllerTestCase::migrationUp('options');
        }
    }

    protected function tearDown()
    {
        if ($this->count()) {
            ControllerTestCase::migrationDown('options');
        }
    }
}
