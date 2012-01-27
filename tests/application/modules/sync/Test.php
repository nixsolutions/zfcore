<?php
class Sync_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';

        $suite = new self('Sync');

        $suite->addTest(new PHPUnit_Framework_TestSuite('Sync_IndexControllerTest'));

        return $suite;
    }

    protected function setUp()
    {
        ControllerTestCase::migrationUp('pages');
    }

    protected function tearDown()
    {
        ControllerTestCase::migrationDown('pages');
    }
}
