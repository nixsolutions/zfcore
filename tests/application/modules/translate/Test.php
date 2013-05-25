<?php
class Translate_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'ManagementControllerTest.php';
        
        $suite = new self('Translate');
        $suite->addTest(new PHPUnit_Framework_TestSuite('Translate_ManagementControllerTest'));

        return $suite;
    }


    protected function setUp()
    {
        ControllerTestCase::migrationUp('translate');
    }

    protected function tearDown()
    {
        ControllerTestCase::migrationDown('translate');
    }

}
