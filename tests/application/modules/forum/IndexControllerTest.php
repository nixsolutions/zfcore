<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Forum_IndexControllerTest extends ControllerTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        parent::migrationUp('forum');
    }

    public function testIndexAction()
    {
        $this->dispatch('/forum');
        $this->assertModule('forum');
        $this->assertController('index');
        $this->assertAction('index');
    }

    public static function tearDownAfterClass()
    {
        parent::migrationDown('forum');
        parent::tearDownAfterClass();
    }
}