<?php

class Options_Test extends PHPUnit_Framework_TestSuite
{


    public static function suite()
    {
        require_once 'models/OptionTest.php';

        $suite = new self('Options');
        $suite->addTest(new PHPUnit_Framework_TestSuite('Model_OptionTest'));

        return $suite;
    }

}
