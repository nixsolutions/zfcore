<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Forum_IndexControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();
        parent::migrationUp('forum');
    }

    public function testIndexAction()
    {
        $this->dispatch('/forum');
        $this->assertModule('forum');
        $this->assertController('index');
        $this->assertAction('index');
    }

    public function tearDown()
    {
        parent::migrationDown('forum');
        parent::tearDown();
    }
}