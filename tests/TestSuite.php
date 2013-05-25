<?php
class TestSuite extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        set_include_path(
            implode(
                PATH_SEPARATOR,
                array(
                    realpath(APPLICATION_PATH . '/../tests/application/modules'),
                    get_include_path(),
                )
            )
        );

        $suite = new PHPUnit_Framework_TestSuite('All tests');

        require_once 'library/Test.php';
        $suite->addTestSuite('Library_Test');

        require_once 'admin/Test.php';
        $suite->addTestSuite('Admin_Test');

        require_once 'blog/Test.php';
        $suite->addTestSuite('Blog_Test');

        require_once 'faq/Test.php';
        $suite->addTestSuite('Faq_Test');

        require_once 'forum/Test.php';
        $suite->addTestSuite('Forum_Test');

        require_once 'install/Test.php';
        $suite->addTestSuite('Install_Test');

        require_once 'mail/Test.php';
        $suite->addTestSuite('Mail_Test');

        require_once 'options/Test.php';
        $suite->addTestSuite('Options_Test');

        require_once 'pages/Test.php';
        $suite->addTestSuite('Pages_Test');

        require_once 'translate/Test.php';
        $suite->addTestSuite('Translate_Test');

        require_once 'users/Test.php';
        $suite->addTestSuite('Users_Test');

        return $suite;
    }
}
