<?php
/**
 * Created by Igor Malinovskiy <u.glide@gmail.com>.
 * Abstract.php
 * Date: 29.11.12
 */
abstract class Core_Tests_Fixtures_Container_Abstract
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * @var array
     */
    public static $fixtures = array();

    /**
     * @param $dbAdapter
     */
    public function __construct(Zend_Db_Adapter_Abstract $dbAdapter)
    {
        $this->_db = $dbAdapter;
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws Core_Tests_Exception
     */
    public function getFixture($name)
    {
        $class = new ReflectionClass($this);
        $fixturesProp = $class->getProperty('fixtures');
        $fixtures = $fixturesProp->getValue($this);

        if (!array_key_exists($name, $fixtures)) {
            throw new Core_Tests_Exception(
                "Fixture {$name} not founded"
            );
        }

        return $fixtures[$name];
    }

    /**
     * @var array
     */
    private $_registeredCleaners = array();

    /**
     * @param $cleanerName
     */
    protected function _registerCleaner($cleanerName)
    {
        $this->_registeredCleaners[] = $cleanerName;
        $this->_registeredCleaners = array_unique($this->_registeredCleaners);
    }

    /**
     * @param array $config
     * @param       $varName
     * @param null  $default
     *
     * @return null
     */
    protected function _loadVarFromConfig(
        array $config, $varName, $default = null
    )
    {
        if (!array_key_exists($varName, $config)) {
            return $default;
        }

        return $config[$varName];
    }

    /**
     * Clean added fixtures from db
     */
    public function clean()
    {
        if (empty($this->_registeredCleaners)) {
            return;
        }

        try {
            foreach ($this->_registeredCleaners as $cleaner) {
                $this->$cleaner();
            }
        } catch (Exception $ex) {
            throw new Core_Tests_Exception(
                "Error occurred on cleaning fixtures : " .  $ex->getMessage()
            );
        }
    }

    /**
     * Return DB adapter with disabled foreign key check
     *
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _getDbAdapterWithFKChecksDisabled()
    {
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $adapter->query('SET FOREIGN_KEY_CHECKS=0');
        return $adapter;
    }
}
