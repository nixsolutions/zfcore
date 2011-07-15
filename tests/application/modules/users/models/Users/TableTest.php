<?php
/**
 * UserTest
 *
 * @category Tests
 * @package  Model
 */
class Model_Users_TableTest extends ControllerTestCase
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
            'password' => 123456);

        $user = $this->_userTable->create($this->_fixture['guest']);
        $user->save();

        $this->_fixture['blocked'] = array(
            'login' => 'testblocked'.time(),
            'email' => 'testblocked@domain.com'.time(),
            'role'  => Users_Model_User::ROLE_USER,
            'status' => Users_Model_User::STATUS_BLOCKED,
            'password' => 123456);

        $user = $this->_userTable->create($this->_fixture['blocked']);
        $user->save();

        $this->_fixture['removed'] = array(
            'login' => 'testremoved'.time(),
            'email' => 'testremoved@domain.com'.time(),
            'role'  => Users_Model_User::ROLE_USER,
            'status' => Users_Model_User::STATUS_REMOVED,
            'password' => 123456);

        $user = $this->_userTable->create($this->_fixture['removed']);
        $user->save();


        $this->_fixture['admin'] = array(
            'login' => 'testadmin'.time(),
            'email' => 'testadmin@domain.com'.time(),
            'role'  => Users_Model_User::ROLE_ADMIN,
            'status' => Users_Model_User::STATUS_ACTIVE,
            'password' => 123456);

        $user = $this->_userTable->create($this->_fixture['admin']);
        $user->save();
    }

    /**
     * Test get user by login
     *
     */
    function testGetByLogin()
    {
        $user = $this->_userTable
                     ->getByLogin($this->_fixture['guest']['login']);

        $this->_fixture['guest']['password'] = null;
        $this->assertEquals(
            array_merge($user->toArray(true), $this->_fixture['guest']),
            $user->toArray(true)
        );
    }

    /**
     * Test get user by email
     *
     */
    function testGetByEmail()
    {
        $user = $this->_userTable
                     ->getByEmail($this->_fixture['guest']['email']);

        $this->_fixture['guest']['password'] = null;
        $this->assertEquals(
            array_merge($user->toArray(true), $this->_fixture['guest']),
            $user->toArray(true)
        );
    }

    /**
     * Remove invironment
     */
    public function tearDown()
    {
        $this->_userTable->delete('1');

        parent::tearDown();
    }
}
