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

        require_once 'default/Test.php';
        $suite->addTestSuite('Default_Test');

        require_once 'faq/Test.php';
        $suite->addTestSuite('Faq_Test');

        require_once 'feedback/Test.php';
        $suite->addTestSuite('Feedback_Test');

        require_once 'forum/Test.php';
        $suite->addTestSuite('Forum_Test');

        require_once 'mail/Test.php';
        $suite->addTestSuite('Mail_Test');

        require_once 'menus/Test.php';
        $suite->addTestSuite('Menu_Test');

        require_once 'options/Test.php';
        $suite->addTestSuite('Options_Test');

        require_once 'pages/Test.php';
        $suite->addTestSuite('Pages_Test');

        require_once 'sync/Test.php';
        $suite->addTestSuite('Sync_Test');

        require_once 'users/Test.php';
        $suite->addTestSuite('Users_Test');

        return $suite;
    }
}
