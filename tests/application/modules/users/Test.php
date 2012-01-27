<?php
class Users_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'IndexControllerTest.php';
        require_once 'LoginControllerTest.php';
        require_once 'ManagementControllerTest.php';
        require_once 'RegisterControllerTest.php';

        require_once 'models/UserTest.php';
        require_once 'models/Users/ManagerTest.php';
        require_once 'models/Users/TableTest.php';

        $suite = new self('Sync');

        $suite->addTest(new PHPUnit_Framework_TestSuite('Users_IndexControllerTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Users_LoginControllerTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Users_ManagementControllerTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Users_RegisterControllerTest'));

        $suite->addTest(new PHPUnit_Framework_TestSuite('Model_UserTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Model_Users_ManagerTest'));
        $suite->addTest(new PHPUnit_Framework_TestSuite('Model_Users_TableTest'));

        return $suite;
    }


}
