<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Blog_IndexControllerTest extends ControllerTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        parent::migrationUp('blog');
    }

    public function testIndexAction()
    {
        $this->dispatch('/blog');
        $this->assertModule('blog');
        $this->assertController('index');
        $this->assertAction('index');
    }

    public static function tearDownAfterClass()
    {
        parent::migrationDown('blog');
        parent::tearDownAfterClass();
    }
}