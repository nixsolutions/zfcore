<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class ErrorControllerTest extends ControllerTestCase
{
    public function testInvalidURL()
    {
        $this->dispatch('foo');

        // see error page in application.yaml
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('notfound');
    }

    public function testInvalidActionURL()
    {
        $this->dispatch('index/foo');

        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('notfound');
    }
}