<?php
class Install_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';
        //require_once 'ErrorControllerTest.php';

        $suite = new self('Install');

        $suite->addTest(new PHPUnit_Framework_TestSuite('Install_IndexControllerTest'));
        //$suite->addTest(new PHPUnit_Framework_TestSuite('ErrorControllerTest'));

        return $suite;
    }

}
