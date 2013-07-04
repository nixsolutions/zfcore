<?php
class Blog_Test extends Core_Tests_PHPUnit_Framework_TestSuite
{


    public static function suite()
    {
        require_once 'IndexControllerTest.php';
        require_once 'PostControllerTest.php';

        $suite = new self('Blog');

        $testClasses = array(
            'Blog_IndexControllerTest',
            'Blog_PostControllerTest'
        );

        $suite->addTests($testClasses);

        return $suite;
    }


    protected function setUp()
    {
        if ($this->count()) {
            ControllerTestCase::migrationUp('categories');
            ControllerTestCase::migrationUp('comments');
            ControllerTestCase::migrationUp('blog');
        }
    }

    protected function tearDown()
    {
        if ($this->count()) {
            ControllerTestCase::migrationDown('blog');
            ControllerTestCase::migrationDown('comments');
            ControllerTestCase::migrationDown('categories');
        }
    }

}
