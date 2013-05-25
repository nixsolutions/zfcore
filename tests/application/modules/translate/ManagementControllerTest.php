<?php
/**
 * User: naxel
 * Date: 25.05.13 18:13
 */

/**
 * ManagementControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Translate_ManagementControllerTest extends ControllerTestCase
{

    /**
     * set up environment
     */
    public function setUp()
    {
        parent::setUp();
        parent::_doLogin(Users_Model_User::ROLE_ADMIN);
    }


    public function testIndex()
    {
        $this->dispatch('/translate/management');
        $this->assertModule('translate');
        $this->assertController('management');
        $this->assertAction('index');
    }


    public function testCreate()
    {
        $this->dispatch('/translate/management/create');
        $this->assertModule('translate');
        $this->assertController('management');
        $this->assertAction('create');
    }

}
