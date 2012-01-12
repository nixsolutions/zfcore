<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class ErrorControllerTest extends ControllerTestCase
{
    /**
     * set up environment
     *
     */
    public function setUp()
    {
        parent::setUp();
    }

    public function testInvalidURL()
    {
        //@todo @fixme
        $this->dispatch('foo');

        // see error page in application.yaml
        $this->assertModule('users');
        $this->assertController('error');
        $this->assertAction('notfound');
    }

    public function testInvalidActionURL()
    {
        $this->dispatch('index/foo');

        $this->assertModule('users');
        $this->assertController('login');
        $this->assertAction('index');
    }

    /**
     * tear Down
     */
    public function tearDown()
    {
        parent::tearDown();
    }
}