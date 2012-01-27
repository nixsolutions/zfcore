<?php
class Forum_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';
        require_once 'PostControllerTest.php';

        $suite = new self('Forum');

        $suite->addTest(new PHPUnit_Framework_TestSuite('Forum_IndexControllerTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Forum_PostControllerTest'));

        return $suite;
    }


    protected function setUp()
    {
        ControllerTestCase::migrationUp('forum');
        ControllerTestCase::migrationUp('comments');
    }

    protected function tearDown()
    {
        ControllerTestCase::migrationDown('comments');
        ControllerTestCase::migrationDown('forum');
    }

}
