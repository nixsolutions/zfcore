<?php
class Pages_Test extends Core_Tests_PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';
        require_once 'ImagesControllerTest.php';
        require_once 'ManagementControllerTest.php';
        require_once 'models/PageTest.php';
        require_once 'models/Page/TableTest.php';

        $suite = new self('Pages');

        $testClasses = array(
            'Pages_IndexControllerTest',
            'Pages_ImagesControllerTest',
            'Pages_ManagementControllerTest',
            'Model_PageTest',
            'Model_Page_ManagerTest'
        );

        $suite->addTests($testClasses);


        return $suite;
    }


    protected function setUp()
    {
        if ($this->count()) {
            ControllerTestCase::migrationUp('pages');
        }
    }

    protected function tearDown()
    {
        if ($this->count()) {
            ControllerTestCase::migrationDown('pages');
        }
    }

}
