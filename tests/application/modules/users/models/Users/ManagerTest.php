<?php
/**
 * UserTest
 *
 * @category Tests
 * @package  Model
 */
class Model_Users_ManagerTest extends ControllerTestCase
{
    /**
     * Setup TestCase
     *
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->_userTable = new Users_Model_Users_Table();
        $this->_userManager = new Users_Model_Users_Manager();
        
        $this->_password = 123456;
        
        $this->_fixture = array();
        
        $this->_fixture['guest'] = array(
            'login'    => 'testguest'.time(),
            'email'    => 'testguest@domain.com'.time(),
            'role'     => Users_Model_User::ROLE_GUEST,
            'hashCode' => 123456,
            'status'   => Users_Model_User::STATUS_REGISTER,
            'password' => 123456
        );
        
        $user = $this->_userTable->create($this->_fixture['guest']);
        $user->save();
        
        $this->_fixture['blocked'] = array(
            'login' => 'testblocked'.time(),
            'email' => 'testblocked@domain.com'.time(),
            'role'  => Users_Model_User::ROLE_USER,
            'status' => Users_Model_User::STATUS_BLOCKED,
            'password' => 123456
        );
                
        $user = $this->_userTable->create($this->_fixture['blocked']);
        $user->save();
        
        $this->_fixture['removed'] = array(
            'login' => 'testremoved'.time(),
            'email' => 'testremoved@domain.com'.time(),
            'role'  => Users_Model_User::ROLE_USER,
            'status' => Users_Model_User::STATUS_REMOVED,
            'password' => 123456
        );
                
        $user = $this->_userTable->create($this->_fixture['removed']);
        $user->save();
        
        
        $this->_fixture['admin'] = array(
            'login' => 'testadmin'.time(),
            'email' => 'testadmin@domain.com'.time(),
            'role'  => Users_Model_User::ROLE_ADMIN,
            'status' => Users_Model_User::STATUS_ACTIVE,
            'password' => 123456
        );
                
        $user = $this->_userTable->create($this->_fixture['admin']);
        $user->save();
    }
    
    
    /**
     * TODO remove hard code
     *
     */
    function testAuthenticate()
    {
        // guest user login/password (non activated)
        $result = Users_Model_Users_Manager::authenticate(
            $this->_fixture['guest']['login'],
            $this->_fixture['guest']['password']
        );
        $this->assertFalse($result);
        
        // blocked user login/password
        $result = Users_Model_Users_Manager::authenticate(
            $this->_fixture['blocked']['login'],
            $this->_fixture['blocked']['password']
        );
            
        $this->assertFalse($result);
        
        // removed login/password
        $result = Users_Model_Users_Manager::authenticate(
            $this->_fixture['removed']['login'],
            $this->_fixture['removed']['password']
        );
            
        $this->assertFalse($result);
        
        // wrong login/password
        $result = Users_Model_Users_Manager::authenticate(
            $this->_fixture['admin']['login'],
            $this->_fixture['admin']['password'].'d'
        );
            
        $this->assertFalse($result);
        
        // right login/password
        $result = Users_Model_Users_Manager::authenticate(
            $this->_fixture['admin']['login'],
            $this->_fixture['admin']['password']
        );
            
        $this->assertTrue($result);
        
        // chech Zend_Auth
        $this->assertEquals(
            Users_Model_User::ROLE_ADMIN,
            Zend_Auth::getInstance()->getIdentity()->role
        );
    }
    
    /**
     * Test Login
     */
    public function testLogin()
    {
        $credential = array('login'    => $this->_fixture['admin']['login'],
                            'password' => $this->_fixture['admin']['password'],
                            'remember' => 0);    
    
        $this->assertTrue($this->_userManager->login($credential));

        $credential = array('login'    => $this->_fixture['admin']['login'],
                            'password' => $this->_fixture['admin']['password'],
                            'remember' => 1);
                                 
        $this->assertTrue($this->_userManager->login($credential));
        
        $credential = array(
            'login'    => $this->_fixture['admin']['login'],
            'password' => $this->_fixture['admin']['password'].'a');
                  
        $this->assertFalse($this->_userManager->login($credential));
    }
    
    /**
     * Test logout
     * 
     */
    public function testLogout()
    {
        $credential = array(
            'login'    => $this->_fixture['admin']['login'],
            'password' => $this->_fixture['admin']['password']);
        $this->_userManager->login($credential);
                                     
        $identity = Zend_Auth::getInstance()->getIdentity();
        
        $this->assertEquals($identity->login, $this->_fixture['admin']['login']);
        
        $this->_userManager->logout();
        
        $identity = Zend_Auth::getInstance()->getIdentity();
        $this->assertNull($identity);
    }
        
    /**
     * Test register
     *
     */
    public function testRegister()
    {
        $user = $this->_userTable
                     ->getByLogin($this->_fixture['admin']['login']);
        $user->delete();
        
        $user = $this->_userManager->register(
            array(
                'login'    => $this->_fixture['admin']['login'],
                'password' => $this->_fixture['admin']['password']
            )
        );
                                     
        $this->assertTrue($user instanceof Core_Db_Table_Row_Abstract);
    }
    
    /**
     * Test confirm registration
     *
     */
    public function testConfirmRegistration()
    {
        $result = $this->_userManager
              ->confirmRegistration($this->_fixture['guest']['hashCode']);
            
        $this->assertTrue($result);
        
        $result = $this->_userManager
              ->confirmRegistration($this->_fixture['guest']['hashCode']);
            
        $this->assertFalse($result);
    }
    
    /**
     * Test forget password
     *
     */
    public function testForgetPassword()
    {
        $result = $this->_userManager
                       ->forgetPassword($this->_fixture['admin']['email']);
        
        $this->assertTrue($result instanceof Core_Db_Table_Row_Abstract);
        
        $result = $this->_userManager->forgetPassword(uniqid());
        
        $this->assertFalse($result);
    }
    
    /**
     * Test forget password confirm
     *
     */
    public function testForgetPasswordConfirm()
    {
        $user = $this->_userManager
                     ->forgetPassword($this->_fixture['admin']['email']);
        $result = $this->_userManager
                       ->forgetPasswordConfirm($user->hashCode, 654321);
        
        $this->assertTrue($result instanceof Core_Db_Table_Row_Abstract, 'password changed');
        
        $user = $this->_userManager
                     ->forgetPassword($this->_fixture['admin']['email']);
                     
        $result = $this->_userManager
                       ->forgetPasswordConfirm($user->hashCode);
        
        $this->assertTrue($result, 'disable change password');
        
        $result = $this->_userManager
                       ->forgetPasswordConfirm($user->hashCode);
        
        $this->assertFalse($result);
    }
    
    
    /**
     * Test generate random password
     */
    public function testGeneratePassword()
    {
        $password = $this->_userManager->generatePassword();
        $this->assertEquals(Users_Model_User::MIN_PASSWORD_LENGTH, strlen($password));
    }
    
    /**
     * Test get filter to all
     */
    public function testGetFilterToAll()
    {

        $filter = array('filter' => 'to all', 'ignore' => 1);
        
        $result = $this->_userManager->getFilter($filter);
        
        //$this->assertType('array', $result);
        $this->assertInternalType('array', $result);
        
        //$this->markTestIncomplete('todo try to find dependency');
//        $this->assertEquals(4, sizeof($result));
    }
    
    /**
     * Test get filter to all active
     */
    public function testGetFilterToAllActive()
    {
        $filter = array('filter' => 'to all active', 'ignore' => 1);
              
        $result = $this->_userManager->getFilter($filter);
                                                       
        //$this->assertType('array', $result);
        $this->assertInternalType('array', $result);
        
        //$this->markTestIncomplete('todo try to find dependency');
//        $this->assertEquals(1, sizeof($result));
    }
    
    /**
     * Test get filter to all disabled
     */
    public function testGetFilterToAllDisabled()
    {
        $filter = array('filter' => 'to all disabled', 'ignore' => 1);
                        
        $result = $this->_userManager->getFilter($filter);
                                                       
        //$this->assertType('array', $result);
        $this->assertInternalType('array', $result);
        
        //$this->markTestIncomplete('todo try to find dependency');
//        $this->assertEquals(1, sizeof($result));
    }
    
    /**
     * Test get filter to all not active last month
     */
    public function testGetFilterToAllNotActiveLastMonth()
    {
        $filter = array('filter' => 'to all not active last month', 
                        'ignore' => 1);
        
        $result = $this->_userManager->getFilter($filter);
                                                       
        //$this->assertType('array', $result);
        $this->assertInternalType('array', $result);
        
        //$this->markTestIncomplete('todo try to find dependency');
//        $this->assertEquals(count($this->_fixture), sizeof($result));
        
        $user = $this->_userTable
                     ->getByLogin($this->_fixture['removed']['login']);
        $user->logined = date('Y-m-d H:i:s');
        $user->save();
        
        $result = $this->_userManager->getFilter($filter);
                                                       
        //$this->assertType('array', $result);
        $this->assertInternalType('array', $result);
        
        //$this->markTestIncomplete('todo try to find dependency');
//        $this->assertEquals(count($this->_fixture)-1, sizeof($result));
    }
    
    /**
     * Test get filter custom email
     */
    public function testGetFilterCustomEmail()
    {
        $filter = array('filter' => 'custom email', 'ignore' => 1);
        
        $result = $this->_userManager->getFilter($filter);
                                                       
        //$this->assertType('array', $result);
        $this->assertInternalType('array', $result);
    }
    
    /**
     * Test get filter exception
     */
    public function testGetFilterException()
    {
       $filter = array('filter' => 'exeption', 'ignore' => 1);
       try {
            $result = $this->_userManager->getFilter($filter);
            $this->fail('you should not see this message');
       } catch (Exception $e) {
            //$this->assertType('string', $e->getMessage());
            $this->assertInternalType('string', $e->getMessage());
       }
    }
    
    /**
     * Remove invironment
     */
    public function tearDown()
    {
        $this->_userTable
             ->getByLogin($this->_fixture['guest']['login'])
             ->delete();
        
        $this->_userTable
             ->getByLogin($this->_fixture['blocked']['login'])
             ->delete();
        
        $this->_userTable
             ->getByLogin($this->_fixture['removed']['login'])
             ->delete();
               
        $this->_userTable
             ->getByLogin($this->_fixture['admin']['login'])
             ->delete();
             
        parent::tearDown();
    }
}
