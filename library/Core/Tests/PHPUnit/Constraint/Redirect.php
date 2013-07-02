<?php
/**
 * Redirection constraints
 *
 * @uses       PHPUnit_Framework_Constraint
 * @category   Core
 * @package    Core_Tests
 * @subpackage PHPUnit
 */
class Core_Tests_PHPUnit_Constraint_Redirect
    extends PHPUnit_Framework_Constraint
{
    /**#@+
     * Assertion type constants
     */
    const ASSERT_REDIRECT       = 'assertRedirect';
    const ASSERT_REDIRECT_TO    = 'assertRedirectTo';
    const ASSERT_REDIRECT_REGEX = 'assertRedirectRegex';
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
        self::ASSERT_REDIRECT,
        self::ASSERT_REDIRECT_TO,
        self::ASSERT_REDIRECT_REGEX,
    );

    /**
     * Pattern to match against
     * @var string
     */
    protected $_match             = null;
    
    /**
     * What is actual redirect
     */
    protected $_actual            = null;

    /**
     * Whether or not assertion is negated
     * @var bool
     */
    protected $_negate            = false;

    /**
     * Constructor; setup constraint state
     *
     * @param null $assertType
     * @param null $match
     *
     * @return void
     */
    public function __construct($assertType = null, $match = null)
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
                    $assertType, __CLASS__
                )
            );
        }

        $this->_assertType = $assertType;
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
     * @param mixed  $other String to examine
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
                'Redirect constraint assertions require a response object'
            );
        }

        $response = $other;

        switch ($this->_assertType) {
            case self::ASSERT_REDIRECT_TO:
                return ($this->_negate)
                    ? $this->_notMatch($response, $this->_match)
                    : $this->_match($response, $this->_match);
            case self::ASSERT_REDIRECT_REGEX:
                return ($this->_negate)
                    ? $this->_notRegex($response, $this->_match)
                    : $this->_regex($response, $this->_match);
            case self::ASSERT_REDIRECT:
            default:
                $headers  = $response->sendHeaders();
                if (isset($headers['location'])) {
                    $redirect = $headers['location'];
                    $redirect = str_replace('Location: ', '', $redirect);
                    $this->_actual = $redirect;
                }
                return ($this->_negate)
                    ? !$response->isRedirect()
                    : $response->isRedirect();
        }
    }

    /**
     * Report Failure
     *
     * @see    PHPUnit_Framework_Constraint for implementation details
     *
     * @param mixed                               $other
     * @param string                              $description
     * @param PHPUnit_Framework_ComparisonFailure $comparisonFailure
     *
     * @return void
     *
     * @throws Core_Tests_PHPUnit_Constraint_Exception
     */
    public function fail($other, $description,
        PHPUnit_Framework_ComparisonFailure $comparisonFailure = NULL)
    {
        require_once 'Core/Tests/PHPUnit/Constraint/Exception.php';
        switch ($this->_assertType) {
            case self::ASSERT_REDIRECT_TO:
                $failure = 'Failed asserting response redirects to "%s"';
                if ($this->_negate) {
                    $failure = 'Failed asserting response DOES NOT '
                        . 'redirect to "%s"';
                }
                $failure = sprintf($failure, $this->_match);
                if (!$this->_negate && $this->_actual) {
                    $failure .= sprintf(
                        PHP_EOL . 'It redirects to "%s".',
                        $this->_actual
                    );
                }
                break;
            case self::ASSERT_REDIRECT_REGEX:
                $failure = 'Failed asserting response redirects to URL '
                    . 'MATCHING "%s"';
                if ($this->_negate) {
                    $failure = 'Failed asserting response DOES NOT redirect '
                        . 'to URL MATCHING "%s"';
                }
                $failure = sprintf($failure, $this->_match);
                if ($this->_actual) {
                    $failure .= sprintf(
                        PHP_EOL . 'It redirects to "%s".',
                        $this->_actual
                    );
                }
                break;
            case self::ASSERT_REDIRECT:
            default:
                $failure = 'Failed asserting response is a redirect';
                if ($this->_negate) {
                    $failure = 'Failed asserting response is NOT a redirect';
                    if ($this->_actual) {
                        $failure .= sprintf(
                            PHP_EOL . 'It redirects to "%s"',
                            $this->_actual
                        );
                    }
                }
                break;
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
     * Check to see if content is matched in selected nodes
     *
     * @param  Zend_Controller_Response_HttpTestCase $response
     * @param  string $match Content to match
     * @return bool
     */
    protected function _match($response, $match)
    {
        if (!$response->isRedirect()) {
            return false;
        }

        $headers  = $response->sendHeaders();
        $redirect = $headers['location'];
        $redirect = str_replace('Location: ', '', $redirect);
        $this->_actual = $redirect;

        return ($redirect == $match);
    }

    /**
     * Check to see if content is NOT matched in selected nodes
     *
     * @param  Zend_Controller_Response_HttpTestCase $response
     * @param  string $match
     * @return bool
     */
    protected function _notMatch($response, $match)
    {
        if (!$response->isRedirect()) {
            return true;
        }

        $headers  = $response->sendHeaders();
        $redirect = $headers['location'];
        $redirect = str_replace('Location: ', '', $redirect);
        $this->_actual = $redirect;

        return ($redirect != $match);
    }

    /**
     * Check to see if content is matched by regex in selected nodes
     *
     * @param  Zend_Controller_Response_HttpTestCase $response
     * @param  string $pattern
     * @return bool
     */
    protected function _regex($response, $pattern)
    {
        if (!$response->isRedirect()) {
            return false;
        }

        $headers  = $response->sendHeaders();
        $redirect = $headers['location'];
        $redirect = str_replace('Location: ', '', $redirect);
        $this->_actual = $redirect;

        return preg_match($pattern, $redirect);
    }

    /**
     * Check to see if content is NOT matched by regex in selected nodes
     *
     * @param  Zend_Controller_Response_HttpTestCase $response
     * @param  string $pattern
     * @return bool
     */
    protected function _notRegex($response, $pattern)
    {
        if (!$response->isRedirect()) {
            return true;
        }

        $headers  = $response->sendHeaders();
        $redirect = $headers['location'];
        $redirect = str_replace('Location: ', '', $redirect);
        $this->_actual = $redirect;

        return !preg_match($pattern, $redirect);
    }
}
