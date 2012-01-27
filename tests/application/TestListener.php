<?php
class TestListener implements PHPUnit_Framework_TestListener
{
    /**
     * time of test
     *
     * @var integer
     */
    protected $_timeTest = 0;
    
    /**
     * time of suite
     *
     * @var integer
     */
    protected $_timeSuite = 0;
    
    public function __construct()
    {
        
    }
    
    public function __destruct()
    {
        ControllerTestCase::appDown();
    }
    
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        echo $this->_colorize("error", "red");
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        echo $this->_colorize("failed", "red");
    }

    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        echo $this->_colorize("incomplete");
    }

    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        echo $this->_colorize("skipped");
    }

    public function startTest(PHPUnit_Framework_Test $test)
    {
        $this->_timeTest = microtime(1);
        $class  = $this->_colorize(get_class($test), 'blue');
        $method = $this->_colorize($test->getName());
        
        echo "\n" . $class . ' -> ' . $method;
    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $time = sprintf('%0.3f sec', microtime(1) - $this->_timeTest);
        
        echo "\t\t" . $test->getCount() . '(Assertions)';
        echo $this->_colorize("\t" . $time, 'green');
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->_timeSuite = microtime(1);
        echo "\n\n".$this->_colorize($suite->getName());
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $time = sprintf('%0.3f sec', microtime(1) - $this->_timeSuite);
        echo $this->_colorize("\nTime: ".$time, 'green');
    }
    
    private function _colorize($text, $color = 'yellow')
    {
        switch ($color) {
            case 'red':
                $color = "1;31m";
                break;
            case 'green':
                $color = "1;32m";
                break;
            case 'blue':
                $color = "1;34m";
                break;
            default:
                $color = "1;33m";
                break;
        }
        return "\033[" . $color . $text . "\033[m";
    }
    
}