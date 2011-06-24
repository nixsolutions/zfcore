<?php

class Users_ManagementControllerTest extends ControllerTestCase
{

    public function setUp()
    {
        parent::setUp();
        
        $this->_doLogin(Users_Model_User::ROLE_ADMIN, 
                        Users_Model_User::STATUS_ACTIVE);
        
        $this->_fixture['0'] = array(
            'login'         => 'test'.time(),
            'password'      => '123456789',
            'firstname'     => 'vasya',
            'lastname'      => 'pupkin',
            'email'         => 'test'.__LINE__.time().'@nixsolutions.com',
            'role'          => Users_Model_User::ROLE_USER,
            'status'        => Users_Model_User::STATUS_BLOCKED,
            'ip'            => '10.10.10.10');
                                
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
            
        $this->_userManager = new Users_Model_Users_Manager();
        $this->_userTable   = new Users_Model_Users_Table();
    }

    /**
     * Test /users/management/create
     *
     */
    public function testAdminUserCreateAction()
    {
        $this->dispatch('/users/management/create');
        $this->request
             ->setMethod('POST')
             ->setPost($this->_fixture['0']);

        $this->dispatch('/users/management/create');
        $this->assertQuery('form#userCreateForm');
        
        $this->assertRedirectTo('/users/management');
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
        $user = $this->_userTable->create();
        $user->setFromArray($this->_fixture['1']);
        $user->save();
        
        $this->_fixture['1'] = array_merge(
            $user->toArray(true),
            array('email' => 'test'.__LINE__.time().'@nixsolutions.com',  'password' => '123456')
        );
        $this->_fixture['1'] = array_filter($this->_fixture['1']);
        
        $this->dispatch('/users/management/edit/id/' . $user->id);
        $this->request
             ->setMethod('POST')
             ->setPost($this->_fixture['1']);
             
        $this->dispatch('/users/management/edit/id/' . $user->id);
        $this->assertQuery('form#userEditForm');
        $this->assertRedirectTo('/users/management');

        $this->_fixture['1']['password'] = '';
        $user->refresh();
        
        unset($this->_fixture['1']['updated']);
        
        $this->assertEquals(
            array_merge($user->toArray(true), $this->_fixture['1']),
            $user->toArray(true)
        );
        $user->delete();
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
        $this->assertQuery('div#gridContainer');
    }
    
    /**
     * Test /users/management/store
     *
     */
    public function testAdminUserStoreAction()
    {
        $this->dispatch('/users/management/store');
        $this->assertModule('users');
        $this->assertController('management');
        $this->assertAction('store');
        $resultBegin = json_decode($this->response->getBody(), true);
//        $this->assertEquals(true, isset($resultBegin['items']));
        if (isset($resultBegin['items'])) {
            $countOfItemsResultBegin = sizeof($resultBegin['items']);
        } else {
            $countOfItemsResultBegin = 0;
        }

        $userFirst = $this->_userTable->create($this->_fixture['0']);
        $userFirst->save();
        $this->dispatch('/users/management/store');
        $result = json_decode($this->response->getBody(), true);
        $this->assertEquals($countOfItemsResultBegin+1, count($result['items']));
        
        $userSecond = $this->_userTable->create($this->_fixture['1']);
        $userSecond->save();
        $this->dispatch('/users/management/store');
        $result = json_decode($this->response->getBody(), true);
        $this->assertEquals($countOfItemsResultBegin+2, count($result['items']));
        
        $this->request->setQuery(array('count' => 1));
        $this->dispatch('/users/management/store/');
        $result = json_decode($this->response->getBody(), true);
        $this->assertEquals(1, count($result['items']));
        
        $userFirst->delete();
        $userSecond->delete();
    }
    
    /**
     * tearDown
     *
     */
    public function tearDown()
    {
        parent::tearDown();
    }
}