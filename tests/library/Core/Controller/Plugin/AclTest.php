<?php

/**
 * ACL Resource
 *
 * @category Tests
 * @package  Core
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 */
class Core_Controller_Plugin_AclTest extends ControllerTestCase
{
    /**
     * Check config parsing
     */
    function testAclConfig()
    {
        $acl = Zend_Registry::get('Acl');

        $this->assertTrue($acl->isAllowed('guest', 'mvc:pages/index', 'index'));

        $this->assertFalse($acl->isAllowed('guest', 'mvc:users/index', 'index'));
        $this->assertFalse($acl->isAllowed('guest', 'mvc:users/login', 'logout'));

        $this->assertTrue($acl->isAllowed('user', 'mvc:pages/index', 'index'));

        $this->assertFalse($acl->isAllowed('user', 'mvc:users/login', 'index'));

        $this->assertTrue($acl->isAllowed('admin', 'mvc:admin/index', 'index'));
    }
}