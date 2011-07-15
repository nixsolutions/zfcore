<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Faq
 */
class Faq_IndexControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();
        parent::migrationUp('faq');
    }

    public function testIndexAction()
    {
        $this->dispatch('/faq');
        $this->assertModule('faq');
        $this->assertController('index');
        $this->assertAction('index');
    }

    public function tearDown()
    {
        parent::migrationDown('faq');
        parent::tearDown();
    }
}