<?php
/**
 * User: naxel
 * Date: 26.06.13 12:53
 *
 * AuthControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Users_AuthControllerTest extends ControllerTestCase
{

    public function testIndexAction()
    {
        $this->_doLogin(Users_Model_User::ROLE_GUEST);

        //Fake data
        Zend_Registry::set('fbConfig', array(
            'appId' => '1111',
            'secret' => '',
            'scope' => 'email,user_checkins'
        ));

        $_SERVER['REQUEST_URI'] = '/users/auth/facebook';

        $this->dispatch('/users/auth/facebook');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains(
            'FacebookStrategy config parameter for "app_secret" expected.',
            $this->getResponse()->getBody()
        );
    }


    public function testStrategyNotFoundIndexAction()
    {
        $this->_doLogin(Users_Model_User::ROLE_GUEST);
        $this->dispatch('/users/auth/strategy');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains('Strategy not found', $this->getResponse()->getBody());
    }


    public function testUserIndexAction()
    {
        $this->_doLogin(Users_Model_User::ROLE_USER);
        $this->dispatch('/users/auth/facebook');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('denied');
    }


    public function testCallbackUserAction()
    {
        $this->_doLogin(Users_Model_User::ROLE_USER);
        $this->dispatch('/users/auth/callback');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('denied');
    }


    public function testCallbackGuestErrorAction()
    {
        $this->_doLogin(Users_Model_User::ROLE_GUEST);
        $this->dispatch('/users/auth/callback');
        $this->assertModule('users');
        $this->assertController('auth');
        $this->assertAction('callback');
        $this->assertRedirectRegex('/^[\/\w]*\/login/');// /en/login
    }

}
