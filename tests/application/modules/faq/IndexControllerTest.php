<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Faq
 */
class Faq_IndexControllerTest extends ControllerTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        parent::migrationUp('faq');
    }
    
    public function testIndexAction()
    {
        $this->dispatch('/faq');
        $this->assertModule('faq');
        $this->assertController('index');
        $this->assertAction('index');
    }
    
    public static function tearDownAfterClass()
    {
        parent::migrationDown('faq');
        parent::tearDownAfterClass();
    }
}