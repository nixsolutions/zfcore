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
        $this->assertModule('default');
        $this->assertController('error');
        $this->assertAction('error');
    }
    
    public function testInvalidActionURL()
    {
        $this->dispatch('index/foo');
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