<?php
class Faq_Test extends Core_Tests_PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';

        $suite = new self('Faq');

        $testClasses = array(
            'Faq_IndexControllerTest'
        );

        $suite->addTests($testClasses);

        return $suite;
    }

    protected function setUp()
    {
        if ($this->count()) {
            ControllerTestCase::migrationUp('faq');
        }
    }

    protected function tearDown()
    {
        if ($this->count()) {
            ControllerTestCase::migrationDown('faq');
        }
    }

}
