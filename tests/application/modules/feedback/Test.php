<?php
class Feedback_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';
        require_once 'ManagementControllerTest.php';

        $suite = new self('Feedback');

        $suite->addTest(new PHPUnit_Framework_TestSuite('Feedback_IndexControllerTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Feedback_ManagementControllerTest'));

        return $suite;
    }


    protected function setUp()
    {
        ControllerTestCase::migrationUp('feedback');
    }

    protected function tearDown()
    {
        ControllerTestCase::migrationDown('feedback');
    }

}
