<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Blog_IndexControllerTest extends ControllerTestCase
{
    public function testIndexAction()
    {
        $this->dispatch('/blog');
        $this->assertModule('blog');
        $this->assertController('index');
        $this->assertAction('index');
    }
}