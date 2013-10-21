<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Payments
 */
class Payments_IndexControllerTest extends ControllerTestCase
{

    /**
     * set up environment
     */
    public function setUp()
    {
        parent::setUp();
        parent::_doLogin(Users_Model_User::ROLE_USER);
    }


    public function testIndexActionByGuest()
    {
        //for guest
        $this->_doLogin(Users_Model_User::ROLE_GUEST);

        $this->dispatch('/payments/index/index');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('denied');
    }

    public function testIndexActionByUser()
    {
        //for user
        $this->_doLogin(Users_Model_User::ROLE_USER);

        $this->dispatch('/payments/index/index');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains('<b>Message:</b> Page not found</p>', $this->getResponse()->getBody());
    }


    /**
     * Index action
     */
    public function testCompleteAction()
    {
        $this->dispatch('/payments/index/complete');
        $this->assertModule('payments');
        $this->assertController('index');
        $this->assertAction('complete');
        $this->assertRedirectTo('/subscriptions');
    }


    public function testCanceledAction()
    {
        $this->dispatch('/payments/index/canceled');
        $this->assertModule('payments');
        $this->assertController('index');
        $this->assertAction('canceled');
        $this->assertRedirectTo('/subscriptions');
    }

}
