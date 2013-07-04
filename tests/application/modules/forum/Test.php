<?php
class Forum_Test extends Core_Tests_PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';
        require_once 'PostControllerTest.php';

        $suite = new self('Forum');

        $testClasses = array(
            'Forum_IndexControllerTest',
            'Forum_PostControllerTest'
        );

        $suite->addTests($testClasses);

        return $suite;
    }


    protected function setUp()
    {
        if ($this->count()) {
            ControllerTestCase::migrationUp('categories');
            ControllerTestCase::migrationUp('comments');
            ControllerTestCase::migrationUp('forum');
        }
    }

    protected function tearDown()
    {
        if ($this->count()) {
            ControllerTestCase::migrationDown('forum');
            ControllerTestCase::migrationDown('comments');
            ControllerTestCase::migrationDown('categories');
        }
    }

}
