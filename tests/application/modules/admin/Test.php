<?php
class Admin_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';
        require_once 'ErrorControllerTest.php';

        $suite = new self('Admin');

        $suite->addTest(new PHPUnit_Framework_TestSuite('Admin_IndexControllerTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Admin_ErrorControllerTest'));

        return $suite;
    }

}
