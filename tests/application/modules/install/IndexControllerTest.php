<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Install
 */
class Install_IndexControllerTest extends ControllerTestCase
{
    /**
     * Requirements action
     */
    public function testRequirementsAction()
    {
        $this->dispatch('/install/requirements');
        $this->assertModule('install');
        $this->assertController('index');
        $this->assertAction('requirements');
    }

    /**
     * Settings action
     */
    public function testSettingsAction()
    {
        $this->dispatch('/install/settings');
        $this->assertModule('install');
        $this->assertController('index');
        $this->assertAction('settings');
        $this->assertNotRedirect();
        $this->assertQuery("form#settingsForm");

        $this->request->setMethod('POST')
            ->setPost(
                array(
                     'timezone' => 'America/New_York',
                     'baseUrl'  => '/',
                     'title'    => 'ZFCore'
                )
            );
        $this->dispatch('/install/settings');
        $this->assertRedirect();

    }

    /**
     * Api action
     */
    public function testApiAction()
    {
        $this->dispatch('/install/api');
        $this->assertModule('install');
        $this->assertController('index');
        $this->assertAction('api');
        $this->assertNotRedirect();
        $this->assertQuery("form#apiForm");
        
        $this->request->setMethod('POST')->setPost(array());
        $this->dispatch('/install/api');
        $this->assertRedirect();
    }

    /**
     * Mail action
     */
    public function testMailAction()
    {
        $this->dispatch('/install/mail');
        $this->assertModule('install');
        $this->assertController('index');
        $this->assertAction('mail');
        $this->assertNotRedirect();
        $this->assertQuery("form#mailForm");

        $this->request->setMethod('POST')
            ->setPost(
                array(
                     'type'  => 'Zend_Mail_Transport_Smtp',
                     'host'  => 'localhost',
                     'email' => 'zfc@nixsolutions.com',
                     'name'  => 'ZFCore Webmaster',
                     'port'  => 2500
                )
            );
        $this->dispatch('/install/mail');
        $this->assertRedirect();
    }

    /**
     * Database Action
     */
    public function testDatabaseAction()
    {
        $this->dispatch('/install/database');
        $this->assertModule('install');
        $this->assertController('index');
        $this->assertAction('database');
        $this->assertNotRedirect();
        $this->assertQuery("form#databaseForm");

        $this->request->setMethod('POST')->setPost(array());

        $this->dispatch('/install/database');
        $this->assertNotRedirect();
    }

    /**
     * Admin action
     */
    public function testAdminAction()
    {
        $this->dispatch('/install/admin');
        $this->assertModule('install');
        $this->assertController('index');
        $this->assertAction('admin');
        $this->assertNotRedirect();
        $this->assertQuery("form#adminForm");

        $this->request->setMethod('POST')->setPost(array());

        $this->dispatch('/install/admin');
        $this->assertNotRedirect();
    }

    /**
     * Admin action
     */
    public function testModulesAction()
    {
        $this->dispatch('/install/modules');
        $this->assertModule('install');
        $this->assertController('index');
        $this->assertAction('modules');
        $this->assertNotRedirect();
        $this->assertQuery("form");

        $this->request->setMethod('POST')->setPost(array());

        $this->dispatch('/install/modules');
        $this->assertRedirect();
    }

    /**
     * Confirm Action
     */
    public function testConfirmAction()
    {
        $this->dispatch('/install/confirm');
        $this->assertModule('install');
        $this->assertController('index');
        $this->assertAction('confirm');
        $this->assertNotRedirect();
        $this->assertQuery("form#confirmForm");

        $this->request->setMethod('POST')->setPost(array('code' => '123456'));
        $this->dispatch('/install/confirm');
        $this->assertNotRedirect();
    }
}


