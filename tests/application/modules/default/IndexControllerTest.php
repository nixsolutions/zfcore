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
        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('index');
    }
}