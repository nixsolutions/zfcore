<?php
class Mail_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'ManagementControllerTest.php';

        $suite = new self('Mail');

        $suite->addTest(new PHPUnit_Framework_TestSuite('Mail_ManagementControllerTest'));

        return $suite;
    }
}
