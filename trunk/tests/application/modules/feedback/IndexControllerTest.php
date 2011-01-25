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
        parent::migrationUp('feedback');
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
        parent::migrationDown('feedback');
        parent::tearDownAfterClass();
    }
}
