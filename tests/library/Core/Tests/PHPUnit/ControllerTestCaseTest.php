<?php
/**
 * Test class for Core_Tests_PHPUnit_ControllerTestCase.
 *
 * @category   Core
 * @package    Core_Tests
 * @subpackage UnitTests
 * @group      Core_Tests
 * @group      Core_Tests_PHPUnit
 */
class Core_Tests_PHPUnit_ControllerTestCaseTest
    extends ControllerTestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $_SESSION = array();
        $this->setExpectedException(null);
        $this->testCase = new Core_Tests_PHPUnit_ControllerTestCaseTest_Concrete();
        $this->testCase->reset();
        $this->testCase->bootstrap = array($this, 'bootstrap');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        $registry = Zend_Registry::getInstance();
        if (isset($registry['router'])) {
            unset($registry['router']);
        }
        if (isset($registry['dispatcher'])) {
            unset($registry['dispatcher']);
        }
        if (isset($registry['plugin'])) {
            unset($registry['plugin']);
        }
        if (isset($registry['viewRenderer'])) {
            unset($registry['viewRenderer']);
        }
        Zend_Session::$_unitTestEnabled = false;
        session_id(uniqid());
    }


    public function testGetFrontControllerShouldReturnFrontController()
    {
        $controller = $this->testCase->getFrontController();
        $this->assertTrue($controller instanceof Zend_Controller_Front);
    }

    public function testGetFrontControllerShouldReturnSameFrontControllerObjectOnRepeatedCalls()
    {
        $controller = $this->testCase->getFrontController();
        $this->assertTrue($controller instanceof Zend_Controller_Front);
        $test = $this->testCase->getFrontController();
        $this->assertSame($controller, $test);
    }

    public function testGetRequestShouldReturnRequestTestCase()
    {
        $request = $this->testCase->getRequest();
        $this->assertTrue($request instanceof Zend_Controller_Request_HttpTestCase);
    }

    public function testGetRequestShouldReturnSameRequestObjectOnRepeatedCalls()
    {
        $request = $this->testCase->getRequest();
        $this->assertTrue($request instanceof Zend_Controller_Request_HttpTestCase);
        $test = $this->testCase->getRequest();
        $this->assertSame($request, $test);
    }

    public function testGetResponseShouldReturnResponseTestCase()
    {
        $response = $this->testCase->getResponse();
        $this->assertTrue($response instanceof Zend_Controller_Response_HttpTestCase);
    }

    public function testGetResponseShouldReturnSameResponseObjectOnRepeatedCalls()
    {
        $response = $this->testCase->getResponse();
        $this->assertTrue($response instanceof Zend_Controller_Response_HttpTestCase);
        $test = $this->testCase->getResponse();
        $this->assertSame($response, $test);
    }

    public function testGetQueryShouldReturnQueryTestCase()
    {
        $query = $this->testCase->getQuery();
        $this->assertTrue($query instanceof Zend_Dom_Query);
    }

    public function testGetQueryShouldReturnSameQueryObjectOnRepeatedCalls()
    {
        $query = $this->testCase->getQuery();
        $this->assertTrue($query instanceof Zend_Dom_Query);
        $test = $this->testCase->getQuery();
        $this->assertSame($query, $test);
    }

    public function testOverloadingShouldReturnRequestResponseAndFrontControllerObjects()
    {
        $request         = $this->testCase->getRequest();
        $response        = $this->testCase->getResponse();
        $frontController = $this->testCase->getFrontController();
        $this->assertSame($request, $this->testCase->request);
        $this->assertSame($response, $this->testCase->response);
        $this->assertSame($frontController, $this->testCase->frontController);
    }

    public function testOverloadingShouldPreventSettingRequestResponseAndFrontControllerObjects()
    {
        try {
            $this->testCase->request = new Zend_Controller_Request_Http();
            $this->fail('Setting request object as public property should raise exception');
        } catch (Exception $e) {
            $this->assertContains('not allow', $e->getMessage());
        }

        try {
            $this->testCase->response = new Zend_Controller_Response_Http();
            $this->fail('Setting response object as public property should raise exception');
        } catch (Exception $e) {
            $this->assertContains('not allow', $e->getMessage());
        }

        try {
            $this->testCase->frontController = Zend_Controller_Front::getInstance();
            $this->fail('Setting front controller as public property should raise exception');
        } catch (Exception $e) {
            $this->assertContains('not allow', $e->getMessage());
        }
    }

    public function testResetShouldResetMvcState()
    {
        require_once 'Zend/Controller/Action/HelperBroker.php';
        require_once 'Zend/Controller/Dispatcher/Standard.php';
        require_once 'Zend/Controller/Plugin/ErrorHandler.php';
        require_once 'Zend/Controller/Router/Rewrite.php';
        $request    = $this->testCase->getRequest();
        $response   = $this->testCase->getResponse();
        $router     = new Zend_Controller_Router_Rewrite();
        $dispatcher = new Zend_Controller_Dispatcher_Standard();
        $plugin     = new Zend_Controller_Plugin_ErrorHandler();
        $controller = $this->testCase->getFrontController();
        $controller->setParam('foo', 'bar')
                   ->registerPlugin($plugin)
                   ->setRouter($router)
                   ->setDispatcher($dispatcher);
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $this->testCase->reset();
        $test = $controller->getRouter();
        $this->assertNotSame($router, $test);
        $test = $controller->getDispatcher();
        $this->assertNotSame($dispatcher, $test);
        $this->assertFalse($controller->getPlugin('Zend_Controller_Plugin_ErrorHandler'));
        $test = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $this->assertNotSame($viewRenderer, $test);
        $this->assertNull($controller->getRequest());
        $this->assertNull($controller->getResponse());
        $this->assertNotSame($request, $this->testCase->getRequest());
        $this->assertNotSame($response, $this->testCase->getResponse());
    }

    public function testBootstrapShouldSetRequestAndResponseTestCaseObjects()
    {
        $this->testCase->bootstrap();
        $controller = $this->testCase->getFrontController();
        $request    = $controller->getRequest();
        $response   = $controller->getResponse();
        $this->assertSame($this->testCase->getRequest(), $request);
        $this->assertSame($this->testCase->getResponse(), $response);
    }

    public function testBootstrapShouldIncludeBootstrapFileSpecifiedInPublicBootstrapProperty()
    {
        $this->testCase->bootstrap = dirname(__FILE__) . '/_files/bootstrap.php';
        $this->testCase->bootstrap();
        $controller = $this->testCase->getFrontController();
        $this->assertSame(Zend_Registry::get('router'), $controller->getRouter());
        $this->assertSame(Zend_Registry::get('dispatcher'), $controller->getDispatcher());
        $this->assertSame(Zend_Registry::get('plugin'), $controller->getPlugin('Zend_Controller_Plugin_ErrorHandler'));
        $this->assertSame(Zend_Registry::get('viewRenderer'), Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer'));
    }

    public function testBootstrapShouldInvokeCallbackSpecifiedInPublicBootstrapProperty()
    {
        $this->testCase->bootstrap = array($this, 'bootstrapCallback');
        $this->testCase->bootstrap();
        $controller = $this->testCase->getFrontController();
        $this->assertSame(Zend_Registry::get('router'), $controller->getRouter());
        $this->assertSame(Zend_Registry::get('dispatcher'), $controller->getDispatcher());
        $this->assertSame(Zend_Registry::get('plugin'), $controller->getPlugin('Zend_Controller_Plugin_ErrorHandler'));
        $this->assertSame(Zend_Registry::get('viewRenderer'), Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer'));
    }

    public function bootstrapCallback()
    {
        require_once 'Zend/Controller/Action/HelperBroker.php';
        require_once 'Zend/Controller/Dispatcher/Standard.php';
        require_once 'Zend/Controller/Front.php';
        require_once 'Zend/Controller/Plugin/ErrorHandler.php';
        require_once 'Zend/Controller/Router/Rewrite.php';
        require_once 'Zend/Registry.php';
        $router     = new Zend_Controller_Router_Rewrite();
        $dispatcher = new Zend_Controller_Dispatcher_Standard();
        $plugin     = new Zend_Controller_Plugin_ErrorHandler();
        $controller = Zend_Controller_Front::getInstance();
        $controller->setParam('foo', 'bar')
                   ->registerPlugin($plugin)
                   ->setRouter($router)
                   ->setDispatcher($dispatcher);
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        Zend_Registry::set('router', $router);
        Zend_Registry::set('dispatcher', $dispatcher);
        Zend_Registry::set('plugin', $plugin);
        Zend_Registry::set('viewRenderer', $viewRenderer);
    }

    public function testDispatchShouldDispatchSpecifiedUrl()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/bar');
        $request  = $this->testCase->getRequest();
        $response = $this->testCase->getResponse();
        $content  = $response->getBody();
        $this->assertEquals('zend-test-php-unit-foo', $request->getControllerName(), $content);
        $this->assertEquals('bar', $request->getActionName());
        $this->assertContains('FooController::barAction', $content, $content);
    }

    public function testAssertQueryShouldDoNothingForValidResponseContent()
    {
        $this->getFrontController()->setControllerDirectory(realpath(dirname(__FILE__)) . '/_files/application/controllers', 'default');
        $this->dispatch('/zend-test-php-unit-foo/baz');
        $body = $this->getResponse()->getBody();
        $this->assertQuery('div#foo legend.bar', $body);
        $this->assertQuery('div#foo legend.baz', $body);
        $this->assertQuery('div#foo legend.bat', $body);
        $this->assertNotQuery('div#foo legend.bogus', $body);
        $this->assertQueryContentContains('legend.bat', 'La di da', $body);
        $this->assertQueryContentContains('legend.numeric', 42, $body);
        $this->assertNotQueryContentContains('legend.numeric', 31, $body);
        $this->assertNotQueryContentContains('legend.bat', 'La do da', $body);
        $this->assertQueryContentRegex('legend.bat', '/d[a|i]/i', $body);
        $this->assertNotQueryContentRegex('legend.bat', '/d[o|e]/i', $body);
        $this->assertQueryCountMin('div#foo legend.bar', 2, $body);
        $this->assertQueryCount('div#foo legend.bar', 2, $body);
        $this->assertQueryCountMin('div#foo legend.bar', 2, $body);
        $this->assertQueryCountMax('div#foo legend.bar', 2, $body);
    }

    /**
     * @group ZF-4673
     */
    public function testAssertionsShouldIncreasePhpUnitAssertionCounter()
    {
        $this->assertTrue(0 == $this->getNumAssertions());
        $this->testAssertQueryShouldDoNothingForValidResponseContent();
        $this->assertTrue(14 == $this->getNumAssertions());
    }

    public function testAssertQueryShouldThrowExceptionsForInValidResponseContent()
    {
        $this->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->dispatch('/zend-test-php-unit-foo/baz');
        try {
            $this->assertNotQuery('div#foo legend.bar');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertQuery('div#foo legend.bogus');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertNotQueryContentContains('legend.bat', 'La di da');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertQueryContentContains('legend.bat', 'La do da');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertNotQueryContentRegex('legend.bat', '/d[a|i]/i');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertQueryContentRegex('legend.bat', '/d[o|e]/i');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertQueryCountMin('div#foo legend.bar', 3);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertQueryCount('div#foo legend.bar', 1);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertQueryCountMin('div#foo legend.bar', 3);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertQueryCountMax('div#foo legend.bar', 1);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
    }

    public function testAssertXpathShouldDoNothingForValidResponseContent()
    {
        $this->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->dispatch('/zend-test-php-unit-foo/baz');
        $this->assertXpath("//div[@id='foo']//legend[contains(@class, ' bar ')]");
        $this->assertXpath("//div[@id='foo']//legend[contains(@class, ' baz ')]");
        $this->assertXpath("//div[@id='foo']//legend[contains(@class, ' bat ')]");
        $this->assertNotXpath("//div[@id='foo']//legend[contains(@class, ' bogus ')]");
        $this->assertXpathContentContains("//legend[contains(@class, ' bat ')]", "La di da");
        $this->assertNotXpathContentContains("//legend[contains(@class, ' bat ')]", "La do da");
        $this->assertXpathContentRegex("//legend[contains(@class, ' bat ')]", "/d[a'i]/i");
        $this->assertNotXpathContentRegex("//legend[contains(@class, ' bat ')]", "/d[o'e]/i");
        $this->assertXpathCountMin("//div[@id='foo']//legend[contains(@class, ' bar ')]", 2);
        $this->assertXpathCount("//div[@id='foo']//legend[contains(@class, ' bar ')]", 2);
        $this->assertXpathCountMin("//div[@id='foo']//legend[contains(@class, ' bar ')]", 2);
        $this->assertXpathCountMax("//div[@id='foo']//legend[contains(@class, ' bar ')]", 2);
    }

    public function testAssertXpathShouldThrowExceptionsForInValidResponseContent()
    {
        $this->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->dispatch('/zend-test-php-unit-foo/baz');
        try {
            $this->assertNotXpath("//div[@id='foo']//legend[contains(@class, ' bar ')]");
            $this->fail("Invalid assertions should throw exceptions; assertion against //div[@id='foo']//legend[contains(@class, ' bar ')] failed");
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertXpath("//div[@id='foo']//legend[contains(@class, ' bogus ')]");
            $this->fail("Invalid assertions should throw exceptions; assertion against //div[@id='foo']//legend[contains(@class, ' bogus ')] failed");
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertNotXpathContentContains("//legend[contains(@class, ' bat ')]", "La di da");
            $this->fail("Invalid assertions should throw exceptions; assertion against //legend[contains(@class, ' bat ')] failed");
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertXpathContentContains("//legend[contains(@class, ' bat ')]", 'La do da');
            $this->fail("Invalid assertions should throw exceptions; assertion against //legend[contains(@class, ' bat ')] failed");
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertNotXpathContentRegex("//legend[contains(@class, ' bat ')]", '/d[a|i]/i');
            $this->fail("Invalid assertions should throw exceptions; assertion against //legend[contains(@class, ' bat ')] failed");
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertXpathContentRegex("//legend[contains(@class, ' bat ')]", '/d[o|e]/i');
            $this->fail("Invalid assertions should throw exceptions; assertion against //legend[contains(@class, ' bat ')] failed");
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertXpathCountMin("//div[@id='foo']//legend[contains(@class, ' bar ')]", 3);
            $this->fail("Invalid assertions should throw exceptions; assertion against //div[@id='foo']//legend[contains(@class, ' bar ')] failed");
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertXpathCount("//div[@id='foo']//legend[contains(@class, ' bar ')]", 1);
            $this->fail("Invalid assertions should throw exceptions; assertion against //div[@id='foo']//legend[contains(@class, ' bar ')] failed");
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertXpathCountMin("//div[@id='foo']//legend[contains(@class, ' bar ')]", 3);
            $this->fail("Invalid assertions should throw exceptions; assertion against //div[@id='foo']//legend[contains(@class, ' bar ')] failed");
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->assertXpathCountMax("//div[@id='foo']//legend[contains(@class, ' bar ')]", 1);
            $this->fail("Invalid assertions should throw exceptions; assertion against //div[@id='foo']//legend[contains(@class, ' bar ')] failed");
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
        }
    }

    public function testRedirectAssertionsShouldDoNothingForValidAssertions()
    {
        $this->getResponse()->setRedirect('/foo');
        $this->assertRedirect();
        $this->assertRedirectTo('/foo', var_export($this->getResponse()->sendHeaders(), 1));
        $this->assertRedirectRegex('/FOO$/i');

        $this->reset();
        $this->assertNotRedirect();
        $this->assertNotRedirectTo('/foo');
        $this->assertNotRedirectRegex('/FOO$/i');
        $this->getResponse()->setRedirect('/foo');
        $this->assertNotRedirectTo('/bar');
        $this->assertNotRedirectRegex('/bar/i');
    }

    public function testHeaderAssertionShouldDoNothingForValidComparison()
    {
        $this->getResponse()->setHeader('Content-Type', 'x-application/my-foo');
        $this->assertResponseCode(200);
        $this->assertNotResponseCode(500);
        $this->assertHeader('Content-Type');
        $this->assertNotHeader('X-Bogus');
        $this->assertHeaderContains('Content-Type', 'my-foo');
        $this->assertNotHeaderContains('Content-Type', 'my-bar');
        $this->assertHeaderRegex('Content-Type', '#^[a-z-]+/[a-z-]+$#i');
        $this->assertNotHeaderRegex('Content-Type', '#^\d+#i');
    }

    public function testHeaderAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->getResponse()->setHeader('Content-Type', 'x-application/my-foo');
        try {
            $this->assertResponseCode(500);
            $this->fail();
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->assertNotResponseCode(200);
            $this->fail();
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->assertNotHeader('Content-Type');
            $this->fail();
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->assertHeader('X-Bogus');
            $this->fail();
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->assertNotHeaderContains('Content-Type', 'my-foo');
            $this->fail();
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->assertHeaderContains('Content-Type', 'my-bar');
            $this->fail();
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->assertNotHeaderRegex('Content-Type', '#^[a-z-]+/[a-z-]+$#i');
            $this->fail();
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->assertHeaderRegex('Content-Type', '#^\d+#i');
            $this->fail();
        } catch (Core_Tests_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
    }

    public function testModuleAssertionShouldDoNothingForValidComparison()
    {
        $this->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->dispatch('/zend-test-php-unit-foo/baz');
        $this->assertModule('default');
        $this->assertNotModule('zend-test-php-unit-foo');
    }

    public function testModuleAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->dispatch('/zend-test-php-unit-foo/baz');
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError');
        $this->assertModule('zend-test-php-unit-foo');
        $this->assertNotModule('default');
    }

    public function testControllerAssertionShouldDoNothingForValidComparison()
    {
        $this->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->dispatch('/zend-test-php-unit-foo/baz');
        $this->assertController('zend-test-php-unit-foo');
        $this->assertNotController('baz');
    }

    public function testControllerAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->dispatch('/zend-test-php-unit-foo/baz');
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError');
        $this->assertController('baz');
        $this->assertNotController('zend-test-php-unit-foo');
    }

    public function testActionAssertionShouldDoNothingForValidComparison()
    {
        $this->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->dispatch('/zend-test-php-unit-foo/baz');
        $this->assertAction('baz');
        $this->assertNotAction('zend-test-php-unit-foo');
    }

    public function testActionAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->dispatch('/foo/baz');
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError');
        $this->assertAction('foo');
        $this->assertNotAction('baz');
    }

    public function testRouteAssertionShouldDoNothingForValidComparison()
    {
        $this->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->dispatch('/zend-test-php-unit-foo/baz');
        $this->assertRoute('default');
        $this->assertNotRoute('zend-test-php-unit-foo');
    }

    public function testRouteAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->dispatch('/foo/baz');
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError');
        $this->assertRoute('foo');
        $this->assertNotRoute('default');
    }

    public function testResetShouldResetSessionArray()
    {
        $this->assertTrue(empty($_SESSION));
        $_SESSION = array('foo' => 'bar', 'bar' => 'baz');
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $_SESSION, var_export($_SESSION, 1));
        $this->testCase->reset();
        $this->assertTrue(empty($_SESSION));
    }

    public function testResetShouldUnitTestEnableZendSession()
    {
        $this->testCase->reset();
        $this->assertTrue(Zend_Session::$_unitTestEnabled);
    }

    public function testResetResponseShouldClearResponseObject()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $response = $this->testCase->getResponse();
        $this->testCase->resetResponse();
        $test = $this->testCase->getResponse();
        $this->assertNotSame($response, $test);
    }

    /**
     * @group ZF-4511
     */
    public function testResetRequestShouldClearRequestObject()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $request = $this->testCase->getRequest();
        $this->testCase->resetRequest();
        $test = $this->testCase->getRequest();
        $this->assertNotSame($request, $test);
    }

    public function testResetResponseShouldClearAllViewPlaceholders()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->initView();
        $view = $viewRenderer->view;
        $view->addHelperPath('Zend/Dojo/View/Helper', 'Zend_Dojo_View_Helper');
        $view->dojo()->setCdnVersion('1.1.0')
                     ->requireModule('dojo.parser')
                     ->enable();
        $view->headTitle('Foo');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $response = $this->testCase->getResponse();
        $this->testCase->resetResponse();

        $view = new Zend_View();
        $view->addHelperPath('Zend/Dojo/View/Helper', 'Zend_Dojo_View_Helper');
        $this->assertFalse($view->dojo()->isEnabled(), 'Dojo is enabled? ', $view->dojo());
        $this->assertNotContains('Foo', $view->headTitle()->__toString(), 'Head title persisted?');
    }

    /**
     * @group ZF-4070
     */
    public function testQueryParametersShouldPersistFollowingDispatch()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $request = $this->testCase->request;
        $request->setQuery('mr', 'proper')
                ->setQuery('james', 'bond');

        $this->assertEquals('proper', $request->getQuery('mr'), '(pre) Failed retrieving mr parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('bond', $request->getQuery('james'), '(pre) Failed retrieving james parameter: ' . var_export($request->getQuery(), 1));

        $this->testCase->dispatch('/');

        $this->assertEquals('proper', $request->getQuery('mr'), '(post) Failed retrieving mr parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('bond', $request->getQuery('james'), '(post) Failed retrieving james parameter: ' . var_export($request->getQuery(), 1));
    }

    /**
     * @group ZF-4070
     */
    public function testQueryStringShouldNotOverwritePreviouslySetQueryParameters()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $request = $this->testCase->request;
        $request->setQuery('mr', 'proper')
                ->setQuery('james', 'bond');

        $this->assertEquals('proper', $request->getQuery('mr'), '(pre) Failed retrieving mr parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('bond', $request->getQuery('james'), '(pre) Failed retrieving james parameter: ' . var_export($request->getQuery(), 1));

        $this->testCase->dispatch('/?spy=super');

        $this->assertEquals('super', $request->getQuery('spy'), '(post) Failed retrieving spy parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('proper', $request->getQuery('mr'), '(post) Failed retrieving mr parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('bond', $request->getQuery('james'), '(post) Failed retrieving james parameter: ' . var_export($request->getQuery(), 1));
    }

    /**
     * @group ZF-3979
     */
    public function testSuperGlobalArraysShouldBeClearedDuringSetUp()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $request = $this->testCase->request;
        $request->setQuery('mr', 'proper')
                ->setPost('foo', 'bar')
                ->setCookie('bar', 'baz');

        $this->testCase->setUp();
        $this->assertNull($request->getQuery('mr'), 'Retrieved mr get parameter: ' . var_export($request->getQuery(), 1));
        $this->assertNull($request->getPost('foo'), 'Retrieved foo post parameter: ' . var_export($request->getPost(), 1));
        $this->assertNull($request->getCookie('bar'), 'Retrieved bar cookie parameter: ' . var_export($request->getCookie(), 1));
    }

    /**
     * @group ZF-4511
     */
    public function testResetRequestShouldClearPostAndQueryParameters()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->getRequest()->setPost(array(
            'foo' => 'bar',
        ));
        $this->testCase->getRequest()->setQuery(array(
            'bar' => 'baz',
        ));
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->testCase->resetRequest();
        $this->assertTrue(empty($_POST));
        $this->assertTrue(empty($_GET));
    }

    /**
     * @group ZF-7839
     */
    public function testTestCaseShouldAllowUsingApplicationObjectAsBootstrap()
    {
        require_once 'Zend/Application.php';
        $application = new Zend_Application('testing', array(
            'resources' => array(
                'frontcontroller' => array(
                    'controllerDirectory' => dirname(__FILE__) . '/_files/application/controllers',
                ),
            ),
        ));
        $this->testCase->bootstrap = $application;
        $this->testCase->bootstrap();
        $this->assertEquals(
            $application->getBootstrap()->getResource('frontcontroller'),
            $this->testCase->getFrontController()
        );
    }

    /**
     * @group ZF-8193
     */
    public function testWhenApplicationObjectUsedAsBootstrapTestCaseShouldExecuteBootstrapRunMethod()
    {
        require_once 'Zend/Application.php';
        $application = new Zend_Application('testing', array(
            'resources' => array(
                'frontcontroller' => array(
                    'controllerDirectory' => dirname(__FILE__) . '/_files/application/controllers',
                ),
            ),
        ));
        $this->testCase->bootstrap = $application;
        $this->testCase->bootstrap();
        $this->testCase->dispatch('/');
        $front = $application->getBootstrap()->getResource('frontcontroller');
        $boot  = $front->getParam('bootstrap');
        $type  = is_object($boot)
               ? get_class($boot)
               : gettype($boot);
        $this->assertTrue($boot === $this->testCase->bootstrap->getBootstrap(), $type);
    }

    /**
     * @group ZF-7496
     * @dataProvider providerRedirectWorksAsExpectedFromHookMethodsInActionController
     */
    public function testRedirectWorksAsExpectedFromHookMethodsInActionController($dispatchTo)
    {
        $this->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->dispatch($dispatchTo);
        $this->assertRedirectTo('/login');
        $this->assertNotEquals('action body', $this->testCase->getResponse()->getBody());
    }

    /**
     * Data provider for testRedirectWorksAsExpectedFromHookMethodsInActionController
     * @return array
     */
    public function providerRedirectWorksAsExpectedFromHookMethodsInActionController()
    {
        return array(
            array('/zend-test-redirect-from-init/baz'),
            array('/zend-test-redirect-from-pre-dispatch/baz')
        );
    }

    /**
     * @group ZF-7496
     * @dataProvider providerRedirectWorksAsExpectedFromHookMethodsInFrontControllerPlugin
     */
    public function testRedirectWorksAsExpectedFromHookMethodsInFrontControllerPlugin($pluginName)
    {
        require_once dirname(__FILE__) . "/_files/application/plugins/RedirectFrom{$pluginName}.php";
        $className = "Application_Plugin_RedirectFrom{$pluginName}";

        $fc = $this->getFrontController();
        $fc->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers')
           ->registerPlugin(new $className());
        $this->dispatch('/');
        $this->assertRedirectTo('/login');
        $this->assertNotEquals('action body', $this->getResponse()->getBody());
    }
    
    /**
     * Data provider for testRedirectWorksAsExpectedFromHookMethodsInFrontControllerPlugin
     * @return array
     */
    public function providerRedirectWorksAsExpectedFromHookMethodsInFrontControllerPlugin()
    {
        return array(
            array('RouteStartup'),
            array('RouteShutdown'),
            array('DispatchLoopStartup'),
            array('PreDispatch')
        );
    }
}

// Concrete test case class for testing purposes
class Core_Tests_PHPUnit_ControllerTestCaseTest_Concrete
    extends Core_Tests_PHPUnit_ControllerTestCase
{
}
