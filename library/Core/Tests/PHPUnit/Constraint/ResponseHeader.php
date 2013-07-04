<?php
/**
 * Response header PHPUnit Constraint
 *
 * @uses       PHPUnit_Framework_Constraint
 * @category   Core
 * @package    Core_Tests
 * @subpackage PHPUnit
 */
class Core_Tests_PHPUnit_Constraint_ResponseHeader
    extends PHPUnit_Framework_Constraint
{
    /**#@+
     * Assertion type constants
     */
    const ASSERT_RESPONSE_CODE   = 'assertResponseCode';
    const ASSERT_HEADER          = 'assertHeader';
    const ASSERT_HEADER_CONTAINS = 'assertHeaderContains';
    const ASSERT_HEADER_REGEX    = 'assertHeaderRegex';
    /**#@-*/

    /**
     * Current assertion type
     * @var string
     */
    protected $_assertType      = null;

    /**
     * Available assertion types
     * @var array
     */
    protected $_assertTypes     = array(
        self::ASSERT_RESPONSE_CODE,
        self::ASSERT_HEADER,
        self::ASSERT_HEADER_CONTAINS,
        self::ASSERT_HEADER_REGEX,
    );

    /**
     * @var int Response code
     */
    protected $_code              = 200;
    
    /**
     * @var int Actual response code
     */
    protected $_actualCode        = null;

    /**
     * @var string Header
     */
    protected $_header            = null;

    /**
     * @var string pattern against which to compare header content
     */
    protected $_match             = null;

    /**
     * Whether or not assertion is negated
     * @var bool
     */
    protected $_negate            = false;

    /**
     * Constructor; setup constraint state
     *
     * @param null $assertType
     * @param null $code
     * @param null $header
     * @param null $match
     *
     * @return void
     */
    public function __construct($assertType = null, $code = null,
        $header = null, $match = null)
    {
        if (strstr($assertType, 'Not')) {
            $this->setNegate(true);
            $assertType = str_replace('Not', '', $assertType);
        }

        if (!in_array($assertType, $this->_assertTypes)) {
            require_once 'Core/Tests/PHPUnit/Constraint/Exception.php';
            throw new Core_Tests_PHPUnit_Constraint_Exception(
                sprintf(
                    'Invalid assertion type "%s" provided to %s constraint',
                    $assertType,
                    __CLASS__
                )
            );
        }

        $this->_assertType = $assertType;
        $this->_code = $code ? $code : $this->_code;
        $this->_header = $header;
        $this->_match = $match;
    }

    /**
     * Indicate negative match
     *
     * @param  bool $flag
     * @return void
     */
    public function setNegate($flag = true)
    {
        $this->_negate = $flag;
    }

    /**
     * Evaluate an object to see if it fits the constraints
     *
     * @param Zend_Controller_Response_Abstract $other
     * @param string $description
     * @param bool   $returnResult
     *
     * @return bool|mixed
     * @throws Core_Tests_PHPUnit_Constraint_Exception
     */
    public function evaluate($other, $description = '', $returnResult = false)
    {
        if (!$other instanceof Zend_Controller_Response_Abstract) {
            require_once 'Core/Tests/PHPUnit/Constraint/Exception.php';
            throw new Core_Tests_PHPUnit_Constraint_Exception(
                'Header constraint assertions require a response object'
            );
        }

        $response = $other;

        switch ($this->_assertType) {
            case self::ASSERT_RESPONSE_CODE:
                return ($this->_negate)
                    ? $this->_notCode($response, $this->_code)
                    : $this->_code($response, $this->_code);
            case self::ASSERT_HEADER:
                return ($this->_negate)
                    ? $this->_notHeader($response, $this->_header)
                    : $this->_header($response, $this->_header);
            case self::ASSERT_HEADER_CONTAINS:
                return ($this->_negate)
                    ? $this->_notHeaderContains(
                        $response,
                        $this->_header,
                        $this->_match
                    )
                    : $this->_headerContains(
                        $response,
                        $this->_header,
                        $this->_match
                    );
            case self::ASSERT_HEADER_REGEX:
                return ($this->_negate)
                    ? $this->_notHeaderRegex(
                        $response,
                        $this->_header,
                        $this->_match
                    )
                    : $this->_headerRegex(
                        $response,
                        $this->_header,
                        $this->_match
                    );
            default:
                require_once 'Core/Tests/PHPUnit/Constraint/Exception.php';
                throw new Core_Tests_PHPUnit_Constraint_Exception(
                    'Invalid assertion type ' . __FUNCTION__
                );
        }
    }

    /**
     * Report Failure
     *
     * @see    PHPUnit_Framework_Constraint for implementation details
     *
     * @param  mixed $other
     * @param  string $description Additional message to display
     * @param  PHPUnit_Framework_ComparisonFailure $comparisonFailure
     *
     * @throws PHPUnit_Framework_ExpectationFailedException
     */
    /**
     * @param mixed                               $other
     * @param string                              $description
     * @param PHPUnit_Framework_ComparisonFailure $comparisonFailure
     *
     * @throws Core_Tests_PHPUnit_Constraint_Exception
     */
    public function fail($other, $description,
        PHPUnit_Framework_ComparisonFailure $comparisonFailure = NULL)
    {
        require_once 'Core/Tests/PHPUnit/Constraint/Exception.php';
        switch ($this->_assertType) {
            case self::ASSERT_RESPONSE_CODE:
                $failure = 'Failed asserting response code "%s"';
                if ($this->_negate) {
                    $failure = 'Failed asserting response code IS NOT "%s"';
                }
                $failure = sprintf($failure, $this->_code);
                if (!$this->_negate && $this->_actualCode) {
                    $failure .= sprintf(
                        PHP_EOL . 'Was "%s"', $this->_actualCode
                    );
                }
                break;
            case self::ASSERT_HEADER:
                $failure = 'Failed asserting response header "%s" found';
                if ($this->_negate) {
                    $failure = 'Failed asserting response response header '
                        . '"%s" WAS NOT found';
                }
                $failure = sprintf($failure, $this->_header);
                break;
            case self::ASSERT_HEADER_CONTAINS:
                $failure = 'Failed asserting response header "%s" exists '
                    . 'and contains "%s"';
                if ($this->_negate) {
                    $failure = 'Failed asserting response header "%s" '
                        . 'DOES NOT CONTAIN "%s"';
                }
                $failure = sprintf($failure, $this->_header, $this->_match);
                break;
            case self::ASSERT_HEADER_REGEX:
                $failure = 'Failed asserting response header "%s" exists '
                    . 'and matches regex "%s"';
                if ($this->_negate) {
                    $failure = 'Failed asserting response header "%s" '
                        . 'DOES NOT MATCH regex "%s"';
                }
                $failure = sprintf($failure, $this->_header, $this->_match);
                break;
            default:
                throw new Core_Tests_PHPUnit_Constraint_Exception(
                    'Invalid assertion type ' . __FUNCTION__
                );
        }

        if (!empty($description)) {
            $failure = $description . "\n" . $failure;
        }

        throw new Core_Tests_PHPUnit_Constraint_Exception($failure);
    }

    /**
     * Complete implementation
     *
     * @return string
     */
    public function toString()
    {
        return '';
    }

    /**
     * Compare response code for positive match
     *
     * @param  Zend_Controller_Response_Abstract $response
     * @param  int $code
     * @return bool
     */
    protected function _code(Zend_Controller_Response_Abstract $response, $code)
    {
        $test = $this->_getCode($response);
        $this->_actualCode = $test;
        return ($test == $code);
    }

    /**
     * Compare response code for negative match
     *
     * @param  Zend_Controller_Response_Abstract $response
     * @param  int $code
     * @return bool
     */
    protected function _notCode(
        Zend_Controller_Response_Abstract $response, $code)
    {
        $test = $this->_getCode($response);
        return ($test != $code);
    }

    /**
     * Retrieve response code
     *
     * @param  Zend_Controller_Response_Abstract $response
     * @return int
     */
    protected function _getCode(Zend_Controller_Response_Abstract $response)
    {
        $test = $response->getHttpResponseCode();
        if (null === $test) {
            $test = 200;
        }
        return $test;
    }

    /**
     * Positive check for response header presence
     *
     * @param  Zend_Controller_Response_Abstract $response
     * @param  string $header
     * @return bool
     */
    protected function _header(
        Zend_Controller_Response_Abstract $response, $header)
    {
        return (null !== $this->_getHeader($response, $header));
    }

    /**
     * Negative check for response header presence
     *
     * @param  Zend_Controller_Response_Abstract $response
     * @param  string $header
     * @return bool
     */
    protected function _notHeader(
        Zend_Controller_Response_Abstract $response, $header)
    {
        return (null === $this->_getHeader($response, $header));
    }

    /**
     * Retrieve response header
     *
     * @param  Zend_Controller_Response_Abstract $response
     * @param  string $header
     * @return string|null
     */
    protected function _getHeader(
        Zend_Controller_Response_Abstract $response, $header)
    {
        $headers = $response->sendHeaders();
        $header  = strtolower($header);
        if (array_key_exists($header, $headers)) {
            return $headers[$header];
        }
        return null;
    }

    /**
     * Positive check for header contents matching pattern
     *
     * @param  Zend_Controller_Response_Abstract $response
     * @param  string $header
     * @param  string $match
     * @return bool
     */
    protected function _headerContains(
        Zend_Controller_Response_Abstract $response, $header, $match)
    {
        if (null === ($fullHeader = $this->_getHeader($response, $header))) {
            return false;
        }

        $contents = str_replace($header . ': ', '', $fullHeader);

        return (strstr($contents, $match));
    }

    /**
     * Negative check for header contents matching pattern
     *
     * @param  Zend_Controller_Response_Abstract $response
     * @param  string $header
     * @param  string $match
     * @return bool
     */
    protected function _notHeaderContains(
        Zend_Controller_Response_Abstract $response, $header, $match)
    {
        if (null === ($fullHeader = $this->_getHeader($response, $header))) {
            return true;
        }

        $contents = str_replace($header . ': ', '', $fullHeader);

        return (!strstr($contents, $match));
    }

    /**
     * Positive check for header contents matching regex
     *
     * @param  Zend_Controller_Response_Abstract $response
     * @param  string $header
     * @param  string $pattern
     * @return bool
     */
    protected function _headerRegex(
        Zend_Controller_Response_Abstract $response, $header, $pattern)
    {
        if (null === ($fullHeader = $this->_getHeader($response, $header))) {
            return false;
        }

        $contents = str_replace($header . ': ', '', $fullHeader);

        return preg_match($pattern, $contents);
    }

    /**
     * Negative check for header contents matching regex
     *
     * @param  Zend_Controller_Response_Abstract $response
     * @param  string $header
     * @param  string $pattern
     * @return bool
     */
    protected function _notHeaderRegex(
        Zend_Controller_Response_Abstract $response, $header, $pattern)
    {
        if (null === ($fullHeader = $this->_getHeader($response, $header))) {
            return true;
        }

        $contents = str_replace($header . ': ', '', $fullHeader);

        return !preg_match($pattern, $contents);
    }
}
