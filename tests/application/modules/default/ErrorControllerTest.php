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
        $this->dispatch('foo');

        // see error page in application.yaml
        $this->assertModule('default');
        $this->assertController('error');
        $this->assertAction('notfound');
    }
    
    public function testInvalidActionURL()
    {
        $this->dispatch('index/foo');

        // default error page, see logic
        $this->assertModule('default');
        $this->assertController('error');
        $this->assertAction('error');
    }
    
    /**
     * tear Down
     */
    public function tearDown()
    {
        parent::tearDown();
    }
}