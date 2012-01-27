<?php
class Faq_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';

        $suite = new self('Faq');
        $suite->addTest(new PHPUnit_Framework_TestSuite('Faq_IndexControllerTest'));

        return $suite;
    }

}
