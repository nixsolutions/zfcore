<?php
/**
 * User: naxel
 * Date: 04.07.13 11:28
 */

class Core_Tests_PHPUnit_Framework_TestSuite extends PHPUnit_Framework_TestSuite
{

    /**
     * Add names of classes for testing
     *
     * @param array $testClasses
     * @return $this
     */
    public function addTests($testClasses)
    {
        $needTesting = self::isNeedMigrations($testClasses);

        if ($needTesting) {
            foreach ($testClasses as $className) {
                $this->addTest(new PHPUnit_Framework_TestSuite($className));
            }
        }
        return $this;
    }


    /**
     * @return bool|string
     */
    protected static function getFilterString()
    {
        for ($i = 0; $i < count($_SERVER["argv"]); $i++) {
            if ($_SERVER["argv"][$i] === '--filter' && isset($_SERVER["argv"][$i + 1])) {
                return $_SERVER["argv"][$i + 1];
            }
        }
        return false;
    }


    /**
     * @param $testClasses
     * @return bool
     */
    public static function isNeedMigrations($testClasses)
    {
        $filterString = self::getFilterString();

        if ($filterString) {

            foreach ($testClasses as $className) {

                if (strpos($className, $filterString) !== false) {
                    return true;
                }

                $rClass = new ReflectionClass($className);
                foreach ($rClass->getMethods() as $rMethod) {
                    if ($rMethod->class === $className && strpos($rMethod->name, $filterString) !== false) {
                        return true;
                    }
                }
            }
        } else {
            return true;
        }
        return false;
    }

}
