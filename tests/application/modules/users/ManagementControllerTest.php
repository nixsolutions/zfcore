<?php

class Users_ManagementControllerTest extends ControllerTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->_doLogin(
            Users_Model_User::ROLE_ADMIN,
            Users_Model_User::STATUS_ACTIVE
        );

        $this->_fixture['0'] = array(
            'login'         => 'test'.time(),
            'password'      => '123456789',
            'firstname'     => 'vasya',
            'lastname'      => 'pupkin',
            'email'         => 'test'.__LINE__.time().'@nixsolutions.com',
            'role'          => Users_Model_User::ROLE_USER,
            'status'        => Users_Model_User::STATUS_BLOCKED,
            //'ip'            => '10.10.10.10'
        );

        $this->_fixture['1'] = array(
            'login'        => 'test3'.time(),
            'password'      => '123456',
            'firstname'     => 'vasya2',
            'lastname'      => 'pupkin2',
            'date_login'    => date('Y-m-d H:i:s'),
            'email'         => 'test'.__LINE__.time().'@nixsolutions.com',
            'role'          => Users_Model_User::ROLE_ADMIN,
            'status'        => Users_Model_User::STATUS_REGISTER,
            'ip'            => '10.10.10.10',
            'count'         => '5');

        $this->_userManager = new Users_Model_User_Manager();
        $this->_userTable   = new Users_Model_User_Table();
    }

    /**
     * Test /users/management/create
     *
     */
    public function testAdminUserCreateAction()
    {
        $this->dispatch('/users/management/create');
        $this->assertQuery('form#userForm');

        $this->request
             ->setMethod('POST')
             ->setPost($this->_fixture['0']);

        $this->dispatch('/users/management/create');
        $this->assertRedirect();

        $user = $this->_userTable->getByLogin($this->_fixture['0']['login']);

        unset($this->_fixture['0']['password']);

        $this->assertTrue(is_numeric($user->id));

        $this->assertEquals(
            array_merge($user->toArray(true), $this->_fixture['0']),
            $user->toArray(true)
        );
        $user->delete();


    }

    /**
     * Test /users/management/edit
     *
     */
    public function testAdminUserEditAction()
    {
        $user = $this->_userTable->createRow();
        $user->setFromArray($this->_fixture['1']);
        $user->save();

        $this->_fixture['1'] = array_merge(
            $user->toArray(true),
            array('email' => 'test'.__LINE__.time().'@nixsolutions.com',  'password' => '123456')
        );
        $this->_fixture['1'] = array_filter($this->_fixture['1']);

        $this->dispatch('/users/management/edit/id/' . $user->id);
        $this->assertQuery('form#userForm');

        $this->request
             ->setMethod('POST')
             ->setPost($this->_fixture['1']);

        $this->dispatch('/users/management/edit/id/' . $user->id);
        $this->assertRedirect();

        $this->_fixture['1']['password'] = md5($user->salt . $this->_fixture['1']['password']);
        $user->refresh();

        unset($this->_fixture['1']['updated']);

        $this->assertEquals(
            array_merge($user->toArray(true), $this->_fixture['1']),
            $user->toArray(true)
        );
        $user->delete();
    }


    public function testIncorrectUserId()
    {
        //Test on Fatal error: Call to a member function toArray() on a non-object
        $this->dispatch('/users/management/edit/id/100000000000000000');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('notfound');

    }


    /**
     * Test /users/management/delete
     * FIXME: Trying to get property of non-object
     */
    /*public function testAdminUserDeleteAction()
    {
        $user = $this->_userTable->create($this->_fixture['0']);
        $user->save();

        $this->dispatch('/users/management/delete/id/' . $user->id);
        $this->assertEquals('1', $this->response->getBody());

        $user = $this->_userTable->getByLogin($this->_fixture['0']['login']);

        $this->assertNull($user->id);
    }*/

    /**
     * Test /users/management/
     *
     */
    public function testAdminUserIndexAction()
    {
        $this->dispatch('/users/management');
        $this->assertModule('users');
        $this->assertController('management');
        $this->assertAction('index');
        $this->assertQuery('div#grid');
    }

    /**
     * Test /users/management/store
     *
     */
    public function testAdminUserStatsAction()
    {
        $this->dispatch('/users/management/stats');
        $this->assertModule('users');
        $this->assertController('management');
        $this->assertAction('stats');
    }

    /**
     * tearDown
     *
     */
    public function tearDown()
    {
        $this->_userTable->delete('1');
        parent::tearDown();
    }
}