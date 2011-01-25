<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class IndexControllerTest extends ControllerTestCase
{
    /**
     * set up environment
     *
     */
    public function setUp()
    {
        parent::setUp();
    }
    
    public function testIndexAction()
    {
        $this->dispatch('/');
        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('index');
    }

    /**
     *  tear Down
     */
    public function tearDown()
    {
        parent::tearDown();
    }
}