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
        parent::tearDown();
    }
}