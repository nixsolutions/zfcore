<?php

/**
 * @group britan
 */
class Core_Migration_ManagerTest extends ControllerTestCase
{
    const FIXTURE_MODULE = 'simplemodule';

    protected $_manager = null;

    /**
     * @return Core_Migration_Manager
     */
    protected function _getManager()
    {
        if (null === $this->_manager) {
            $this->_manager = new Core_Migration_Manager(
                array(
                    'migrationsSchemaTable'   => '~migrations',
                    'projectDirectoryPath'    => dirname(__FILE__) . '/_env/',
                    'modulesDirectoryPath'    => dirname(__FILE__) . '/_env/application/modules/',
                    'migrationsDirectoryName' => 'migrations',
                )
            );
        }

        return $this->_manager;
    }
    
    public function testSetModulesDirectoryPath()
    { 
        $manager = new Core_Migration_Manager();
        $manager->setModulesDirectoryPath('/test/path');

        $this->assertEquals('/test/path', $manager->getModulesDirectoryPath());
    }

    public function testSetProjectDirectoryPath()
    {
        $manager = new Core_Migration_Manager();
        $manager->setProjectDirectoryPath('/test/path');

        $this->assertEquals('/test/path', $manager->getProjectDirectoryPath());
    }

    /**
     * @dataProvider providerUpSuccess
     * @param $module
     * @param $migration
     * @param $tableFilter
     * @param $expected
     */
    public function testUpSuccess($module, $migration, $tableFilter, $expected)
    {
        $this->_getManager()->up($module, $migration);
        
        $result = Zend_Db_Table_Abstract::getDefaultAdapter()
            ->query("SHOW TABLES LIKE '".$tableFilter."';");

        $this->assertEquals($expected, $result->rowCount());

        while ($tableName = $result->fetchColumn()) {
            Zend_Db_Table_Abstract::getDefaultAdapter()
                ->query("DROP TABLE `".$tableName."`");
        }
    }
    /**
     * Data provider for testUpSuccess
     * @return array
     */
    public function providerUpSuccess()
    {
        return array(
            array(null, null, 'items_0%', 5),
            array(null, '99999999_000000_02', 'items_0%', 3),
            array(self::FIXTURE_MODULE, null, 'items_s%', 5),
            array(self::FIXTURE_MODULE, '99999999_000000_01', 'items_s%', 2)
        );
    }

    /**
     * Test for method `generate`
     */

    public function testGenerateMigrationSuccess()
    {
        $db = new Core_Migration_Adapter_Mysql(Zend_Db_Table::getDefaultAdapter());

        $db->query(Core_Db_Database::dropTable('test_table'));
        $db->createTable('test_table');
        $db->createColumn('test_table', 'col1', Core_Migration_Abstract::TYPE_INT);
        $db->createColumn('test_table', 'col2', Core_Migration_Abstract::TYPE_VARCHAR, 50);


        $diff = $this->_getManager()->generateMigration(null, '', 'test_table', true);


        $compareTo = array(
            'down'=>array(Core_Db_Database::dropTable('test_table')),
            'up'=>array(
                Core_Db_Database::dropTable('test_table'),
                Core_Db_Database::createTable('test_table')
            )
        );

        $this->assertEquals($compareTo, $diff);


        $db->dropTable('test_table');
    }

    /**
     * @dataProvider providerUpExceptions
     * @param $migration
     * @return
     */
    public function testUpExceptions($migration)
    {
        $this->_getManager()->up();

        try {
            $this->_getManager()->up(null, $migration);
        } catch (Exception $expected) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('An expected Exception has not been raised.');
    }

    /**
     * Data provider for testUpExceptions
     * @return array
     */
    public function providerUpExceptions()
    {
        return array(
            array('some_name'),             // Invalid migration name
            array('99999999_000000_04'),    // Current migration
            array('99999999_000000_03'),    // Older then current migration
        );
    }

    /**
     * @dataProvider providerDownSuccess
     * @param $module
     * @param $migration
     * @param $tableFilter
     * @param $expected
     */
    public function testDownSuccess($module, $migration, $tableFilter, $expected)
    {
        $this->_getManager()->up($module);

        $result = Zend_Db_Table_Abstract::getDefaultAdapter()
            ->query("SHOW TABLES LIKE '".$tableFilter."';");

        $this->assertEquals(5, $result->rowCount());

        $this->_getManager()->down($module, $migration);
        
        $result = Zend_Db_Table_Abstract::getDefaultAdapter()
            ->query("SHOW TABLES LIKE '".$tableFilter."';");

        $this->assertEquals($expected, $result->rowCount());

        while ($tableName = $result->fetchColumn()) {
            Zend_Db_Table_Abstract::getDefaultAdapter()
                ->query("DROP TABLE `".$tableName."`");
        }
    }

    /**
     * Data provider for testDownSuccess
     * @return array
     */
    public function providerDownSuccess()
    {
        return array(
            array(null, null, 'items_0%', 0),
            array(null, '99999999_000000_02', 'items_0%', 3),
            array(self::FIXTURE_MODULE, null, 'items_s%', 0),
            array(self::FIXTURE_MODULE, '99999999_000000_01', 'items_s%', 2)
        );
    }

    /**
     * @dataProvider providerRollbackSuccess
     * @param $module
     * @param $step
     * @param $tableFilter
     * @param $expected
     */
    public function testRollbackSuccess($module, $step, $tableFilter, $expected)
    {
        $this->_getManager()->up($module);

        $result = Zend_Db_Table_Abstract::getDefaultAdapter()
            ->query("SHOW TABLES LIKE '".$tableFilter."';");

        $this->assertEquals(5, $result->rowCount());

        $this->_getManager()->rollback($module, $step);

        $result = Zend_Db_Table_Abstract::getDefaultAdapter()
            ->query("SHOW TABLES LIKE '".$tableFilter."';");

        $this->assertEquals($expected, $result->rowCount());

        while ($tableName = $result->fetchColumn()) {
            Zend_Db_Table_Abstract::getDefaultAdapter()
                ->query("DROP TABLE `".$tableName."`");
        }
    }

    /**
     * Data provider for testRollbackSuccess
     * @return array
     */
    public function providerRollbackSuccess()
    {
        return array(
            array(null, '1', 'items_0%', 4),
            array(null, '3', 'items_0%', 2),
            array(self::FIXTURE_MODULE, '1', 'items_s%', 4),
            array(self::FIXTURE_MODULE, '3', 'items_s%', 2)
        );
    }

    protected function tearDown()
    {
        if (null !== $this->_manager) {
            $result = Zend_Db_Table_Abstract::getDefaultAdapter()
                ->query("SHOW TABLES LIKE 'items_%';");

            while ($tableName = $result->fetchColumn()) {
                Zend_Db_Table_Abstract::getDefaultAdapter()
                    ->query("DROP TABLE `".$tableName."`");
            }

            Zend_Db_Table_Abstract::getDefaultAdapter()
                ->query("DROP TABLE `".$this->_getManager()->getMigrationsSchemaTable()."`");
        }

        parent::tearDown();
    }
}