<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Feedback
 */
class Feedback_IndexControllerTest extends ControllerTestCase
{
    /**
     * set up environment
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }
    
    /**
     * Feedback/Index/Index
     * 
     * allow access for admin
     */
    public function testFeedbackIndexAction()
    {
        $this->dispatch('/feedback/');
        
        $this->assertModule('feedback');
        $this->assertController('index');
        $this->assertAction('index');
    }
    
    
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }
}
