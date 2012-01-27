<?php
class Default_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';
        require_once 'ErrorControllerTest.php';

        $suite = new self('Default');

        $suite->addTest(new PHPUnit_Framework_TestSuite('IndexControllerTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('ErrorControllerTest'));

        return $suite;
    }

}
