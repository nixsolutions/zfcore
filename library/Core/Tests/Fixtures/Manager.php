<?php
/**
 * Created by Igor Malinovskiy <u.glide@gmail.com>
 * Date: 28.11.12
 * Time: 23:24
 */

class Core_Tests_Fixtures_Manager
{
    /**
     * Annotation var for determine fixture
     */
    const FIXTURE_ANNOTATION_KEY = 'fixture';

    /**
     * Const's for fixture containers loading
     */
    const FIXTURE_CONTAINER_CLASS_SUFFIX = "_Fixtures_Container";
    const TARGET_TESTCASE_CLASS = 'PHPUnit_Framework_TestCase';
    const MAX_CLASS_NESTING = 5;

    /**
     * Matcher's for parse @fixture annotation
     */
    const MATCHER_TARGET_VAR = '/^\$[a-z]/i';
    const MATCHER_METHOD = '/^([a-z_]+)::([a-z_0-9]+)(\+)*/i';
    const MATCHER_FIXTURE_VAR = '/^([a-z_]+)::(\$[a-z_]+)/i';

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    private $_dbAdapter;

    /**
     * @var Core_Tests_Fixtures_Container_Abstract[]
     */
    private $_containers = array();

    /**
     * @param $dbAdapter
     */
    public function __construct($dbAdapter)
    {
        $this->_dbAdapter = $dbAdapter;
    }

    /**
     * @param ControllerTestCase $testCase
     *
     * @throws Core_Tests_Exception
     */
    public function prepareFixtures(ControllerTestCase $testCase)
    {
        if (!$this->_isFixturesUsedInTest($testCase)) {
            return;
        }

        $fixtures = $this->_getFixturesDataFromAnnotation(
            $testCase
        );

        if (empty($fixtures)) {
            throw new Core_Tests_Exception("Invalid fixtures definition!");
        }

        foreach ($fixtures as $fixture) {

            $fixtureContainer = $this->_loadFixtureContainer($fixture);

            try {

                if (isset($fixture['method'])) {

                    $methodName = $fixture['method'];

                    if (isset($fixture['passDataFromProvider'])
                        && $fixture['passDataFromProvider']) {
                        $dataFromProvider = $this->_getDataFromTestCase(
                            $testCase
                        );

                        /**
                         * If data for current setUp method specified
                         * provide to setUp method only this item of data array
                         */
                        if (is_array($dataFromProvider)
                            && array_key_exists(
                                $methodName, $dataFromProvider
                            )) {
                            $result = $fixtureContainer->$methodName(
                                $dataFromProvider[$methodName]
                            );
                        } else {
                            $result = $fixtureContainer->$methodName(
                                $dataFromProvider
                            );
                        }
                    } else {
                        $result = $fixtureContainer->$methodName();
                    }

                    if (array_key_exists('variable', $fixture)) {
                        $testCase->setFixture(
                            $fixture['variable'],
                            $result
                        );
                    }

                } elseif (isset($fixture['fixtureVar'])) {

                    $targetFixtureName = (isset($fixture['variable'])) ?
                        $fixture['variable'] : $fixture['fixtureVar'];

                    $testCase->setFixture(
                        $targetFixtureName,
                        $fixtureContainer->getFixture(
                            $fixture['fixtureVar']
                        )
                    );
                }

            } catch (Exception $e) {
                 throw new Core_Tests_Exception(
                     "Error occurred on execution
                     {$fixture['module']}::{$fixture['method']}: "
                     . $e->getMessage()
                 );
            }
        }
    }

    /**
     * @param array $fixture
     *
     * @return Core_Tests_Fixtures_Container_Abstract
     * @throws Core_Tests_Exception
     */
    private function _loadFixtureContainer(array $fixture)
    {
        $module = $fixture['module'];

        if (array_key_exists($module, $this->_containers)) {

            $fixtureContainer = $this->_containers[$module];

            if (!
            ($fixtureContainer
                instanceof Core_Tests_Fixtures_Container_Abstract
            )
            ) {
                throw new Core_Tests_Exception(
                    "Invalid Fixture Container in cache"
                );
            }

        } else {
            try {
                $containerClassName = $this->_getClassNameForFixture(
                    $fixture
                );

                if (!class_exists($containerClassName)) {
                    $loader = Zend_Loader_Autoloader::getInstance();
                    $loader->autoload($containerClassName);
                }

                $fixtureContainer = new $containerClassName(
                    $this->_dbAdapter
                );

                $this->_containers[$module] = $fixtureContainer;
            } catch (Exception $e) {
                throw new Core_Tests_Exception(
                    "Can't load fixture container for module "
                    . $module . " : " . $e->getMessage()
                );
            }
        }

        return $fixtureContainer;
    }

    /**
     * @param $fixture
     *
     * @return string
     */
    private function _getClassNameForFixture($fixture)
    {
        return $fixture['module'] . self::FIXTURE_CONTAINER_CLASS_SUFFIX;
    }

    /**
     * @param ControllerTestCase $testCase
     *
     * @return mixed
     * @throws Core_Tests_Exception
     */
    private function _getDataFromTestCase(ControllerTestCase $testCase)
    {
        $testCaseClass = new ReflectionObject($testCase);

        $targetClassFound = false;
        $parentCount = 0;

        while ($parentCount < self::MAX_CLASS_NESTING) {
            $testCaseClass = $testCaseClass->getParentClass();

            if ($testCaseClass->getName() == self::TARGET_TESTCASE_CLASS) {
                $targetClassFound = true;
                break;
            }
            $parentCount++;
        }

        if (!$targetClassFound) {
            throw new Core_Tests_Exception(
                "PHPUnit test case class not found
                    in parents of current test case class"
            );
        }

        try {
            $dataProp = $testCaseClass->getProperty('data');
            $dataProp->setAccessible(true);
            return $dataProp->getValue($testCase);
        } catch (ReflectionException $ex) {
            throw new Core_Tests_Exception(
                "Can't get data by reflection"
            );
        }
    }

    /**
     * @param ControllerTestCase $testCase
     *
     * @return array
     */
    private function _getFixturesDataFromAnnotation(
        ControllerTestCase $testCase
    )
    {
        $fixturesRawData = $testCase->getAnnotations()
            ['method'][self::FIXTURE_ANNOTATION_KEY];

        $fixtures = array();

        foreach ($fixturesRawData as $fixtureData) {
            $fixture = array();
            $fixtureParts = explode(' ', $fixtureData);

            foreach ($fixtureParts as $part) {
                $matches = array();

                if (preg_match(self::MATCHER_TARGET_VAR, $part, $matches)) {
                    $fixture['variable'] = trim(str_replace('$', '', $part));
                } elseif (
                    preg_match(self::MATCHER_METHOD, $part, $matches)
                ) {

                    $fixture['module'] = $matches[1];
                    $fixture['method'] = $matches[2];
                    $fixture['passDataFromProvider'] = (isset($matches[3])) ?
                        true : false;
                } elseif (
                    preg_match(self::MATCHER_FIXTURE_VAR, $part, $matches)
                ) {
                    $fixture['module'] = $matches[1];
                    $fixture['fixtureVar'] = trim(
                        str_replace('$', '', $matches[2])
                    );
                }
            }

            if ($this->_isValidFixture($fixture)) {
                $fixtures[] = $fixture;
            }
        }

        return $fixtures;
    }

    /**
     * @param array $fixture
     *
     * @return bool
     */
    private function _isValidFixture(array $fixture)
    {
        return array_key_exists('module', $fixture)
            && (array_key_exists('method', $fixture)
                || array_key_exists('fixtureVar', $fixture));
    }

    /**
     * @param ControllerTestCase $testCase
     *
     * @return bool
     */
    private function _isFixturesUsedInTest(ControllerTestCase $testCase)
    {
        return array_key_exists(
            self::FIXTURE_ANNOTATION_KEY,
            $testCase->getAnnotations()['method']
        );
    }

    /**
     * @throws Core_Tests_Exception
     */
    public function removeFixtures()
    {
        foreach ($this->_containers as $module => $container) {
            try {
                $container->clean();
            } catch (Exception $e) {
                throw new Core_Tests_Exception(
                    "Error occurred on cleaning fixtures
                        installed from {$module} fixtures container"
                );
            }
        }
    }
}
