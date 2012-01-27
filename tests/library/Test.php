<?php
class Library_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'Zend/VersionTest.php';

        require_once 'Core/Controller/Plugin/AclTest.php';
        require_once 'Core/Dump/ManagerTest.php';
        require_once 'Core/Form/MultipageTest.php';
        require_once 'Core/Migration/ManagerTest.php';

        $suite = new self('Library');

        $suite->addTest(new PHPUnit_Framework_TestSuite('Zend_VersionTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Core_Controller_Plugin_AclTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Core_Dump_ManagerTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Core_Form_MultipageTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Core_Migration_ManagerTest'));

        return $suite;
    }
}
