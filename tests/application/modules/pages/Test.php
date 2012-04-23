<?php
class Pages_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';
        require_once 'ImagesControllerTest.php';
        require_once 'models/PageTest.php';
        require_once 'models/Page/TableTest.php';
        
        $suite = new self('Pages');

        $suite->addTest(new PHPUnit_Framework_TestSuite('Pages_IndexControllerTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Pages_ImagesControllerTest'));

        $suite->addTest(new PHPUnit_Framework_TestSuite('Model_PageTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Model_Page_ManagerTest'));


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
