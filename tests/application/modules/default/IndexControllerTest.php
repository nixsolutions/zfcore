<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class IndexControllerTest extends ControllerTestCase
{
    public function testIndexAction()
    {
        $this->dispatch('/');
        $this->assertModule('users');
        $this->assertController('login');
        $this->assertAction('index');
    }
}