<?php
require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';
require_once 'Zend/Config/Yaml.php';
require_once 'Core/Config/Yaml.php';

/**
 * Controller Test case
 *
 * @category Tests
 */
abstract class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * Application entity
     *
     * @var Zend_Application
     */
    protected $_application;

    /**
     * Migration manager
     *
     * @var Core_Migration_Manager
     */
    protected $_manager;

    /**
     * fixtures put here
     *
     * @var as you wish
     */
    protected $_fixture;

    /**
     * Setup TestCase
     */
    public function setUp()
    {
        $config = APPLICATION_PATH . '/configs/application.yaml';
        $result = new Core_Config_Yaml($config, APPLICATION_ENV);
        $result = $result->toArray();
        $this->bootstrap = new Zend_Application(
            APPLICATION_ENV,
            $result
        );

        parent::setUp();

        $bootstrap = $this->bootstrap->getBootstrap();

        $this->getFrontController()->setParam('bootstrap', $bootstrap);

    }

    /**
     * Init Application
     */
    static public function appInit()
    {
        $config = APPLICATION_PATH . '/configs/application.yaml';
        $result = new Core_Config_Yaml($config, APPLICATION_ENV);
        $result = $result->toArray();

        // Create application, bootstrap, and run
        $application = new Zend_Application(
            APPLICATION_ENV,
            $result
        );

//        $application->bootstrap();
//        $application->bootstrap('Frontcontroller');
        $application->bootstrap();

        self::migration();
    }

    /**
     * Shut down Application
     */
    static public function appDown()
    {
        self::migrationDown();

        try {
            Zend_Db_Table_Abstract::getDefaultAdapter()
                ->query("DROP TABLE `migrations`");
        } catch (Exception $e) {

        }
    }

    /**
     * debug
     *
     * @return string
     */
    public function debug()
    {
        if ($this->getResponse()->isException()) {
            echo "Exceptions: \n";
            print_r($this->getResponse()->getException());
        }
    }

    /**
     * Migrations
     *
     * @param bool $up
     * @param null $module
     * @param null $migration
     */
    static public function migration($up = true, $module = null,
        $migration = null)
    {
        require_once 'Core/Migration/Manager.php';

        if ((null === $migration) &&
            Core_Migration_Manager::isMigration($module)) {
                list($migration, $module) = array($module, null);
        }

        $manager = new Core_Migration_Manager(array(
            'projectDirectoryPath'    => APPLICATION_PATH . '/../',
            'modulesDirectoryPath'    => APPLICATION_PATH . '/modules/',
            'migrationsDirectoryName' => 'migrations',
        ));
        if ($up) {
            $manager->up($module, $migration);
        } else {
            $manager->down($module, $migration);
        }

        foreach ($manager->getMessages() as $message) {
            echo $message ."\n";
        }
    }

    /**
     * Migrations Up
     *
     * @param null $module
     */
    static public function migrationUp($module = null)
    {
        echo "\n";
        self::migration(true, $module);
    }

    /**
     * Migrations Down
     *
     * @param null $module
     */
    static public function migrationDown($module = null)
    {
        echo "\n";
        self::migration(false, $module);
    }

    /**
     * Change environment for user role/status
     * Should be run after setUp() !!!
     *
     * @param string $role
     * @param string $status
     * @return void
     */
    protected static function _doLogin($role = Users_Model_User::ROLE_USER,
        $status = Users_Model_User::STATUS_ACTIVE )
    {
        Zend_Auth::getInstance()->getStorage()
                                ->write(
                                    self::_generateFakeIdentity($role, $status)
                                );
    }


    /**
     * Create user
     *
     * @param string $role
     * @param string $status
     * @return StdClass an identity
     */
    protected static function _generateFakeIdentity(
        $role = Users_Model_User::ROLE_USER,
        $status = Users_Model_User::STATUS_ACTIVE)
    {
        $account = new stdClass();

        $account->login    = 'AutoTest' . date('YmdHis');
        $account->email    = 'autotest' . time() . '@example.org';
        $account->password = md5('password');
        $account->role     = $role;
        $account->status   = $status;
        $account->id       = 75;

        return $account;
    }

    /**
     * Remove environment
     *
     */
    protected function tearDown()
    {
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $dbAdapter -> closeConnection();

        parent::tearDown();
    }
}