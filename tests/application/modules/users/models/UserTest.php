<?php

/**
 * UserTest
 *
 * @category Tests
 * @package  Model
 */
class Model_UserTest extends ControllerTestCase
{
    /**
     * Setup TestCase
     *
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->_fixture = array('login'    => 'testguest'.uniqid(),
                                'email'    => 'testguest@domain.com',
                                'ip'       => '10.10.10.10',
                                'role'     => Users_Model_User::ROLE_GUEST,
                                'status'   => Users_Model_User::STATUS_ACTIVE,
                                'password' => 123456);
        
        $this->_userTable = new Users_Model_Users_Table();
        
        $this->_user = $this->_userTable->create($this->_fixture);
        $this->_user->save();
    }
    
    /**
     * Test get password
     *
     */
    public function testGetPassword()
    {
        $this->assertNull($this->_user->password);
    }
    
    
    /**
     * Test get password
     *
     */
    public function testSetPassword()
    {
        $password = 654321;
        $this->_user->password = $password;
        $this->_user->save();
        
        $this->assertTrue(
            Users_Model_Users_Manager::authenticate($this->_user->login, $password)
        );
    }
    
    /**
     * Test get ip
     *
     */
    public function testGetIp()
    {
        $result = preg_match('/(\d+\.\d+\.\d+\.\d+)/u', $this->_user->ip);
        $this->assertGreaterThan(0, $result);
    }
    
    /**
     * Test set ip
     *
     */
    public function testSetIp()
    {
        $ip = '11.11.11.11';
        $this->_user->ip = $ip;
        $this->_user->save();
        
        $this->assertEquals($ip, $this->_user->ip);
    }
    
    /**
     * test toArray
     *
     */
    public function testToArray()
    {
        //safe method
        $user = $this->_user->toArray(true);
        unset($this->_fixture['password']);
        
        $this->assertEquals(array_merge($user, $this->_fixture), $user);
        
        $this->assertNull($user['password']);
        
        //default method
        $user = $this->_user->toArray();
        $this->assertGreaterThanOrEqual(32, strlen($user['password']));
        
    }
    
    /**
     * Remove environtment
     */
    protected function tearDown()
    {
        $user = $this->_userTable->getByLogin($this->_fixture['login']);
        $user->delete();
        
        parent::tearDown();
    }
}
