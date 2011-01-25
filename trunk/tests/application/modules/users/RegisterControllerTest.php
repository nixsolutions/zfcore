<?php
//require_once dirname(dirname(dirname(__FILE__))) . "/ControllerTestCase.php";

/**
 * RegisterControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Users_RegisterControllerTest extends ControllerTestCase
{
    /**
     * User Model
     *
     * @var Model_User
     */
    protected $_user;

    /**
     * Setup TestCase
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->_userManager = new Model_User_Manager();
        $this->_userTable = new Model_User_Table();

        $this->_fixture = array('login'     => 'testadmin'.time(),
                                'email'     => 'test'.time().'@nixsolutions.com',
                                'status'    => Model_User::STATUS_ACTIVE,
                                'password'  => 'qwerty',
                                'password2' => 'qwerty');

        //clears request and responce
        $this->resetRequest();
        $this->resetResponse();
    }

    /**
     * Gets captcha word
     *
     * @param string $html
     * @return array
     */
    public function getCaptcha($html)
    {
        $dom = new Zend_Dom_Query($html);
        $id = $dom->query('#captcha-id')->current()->getAttribute('value');

        foreach ($_SESSION as $key => $value) {
            if (ereg("Zend_Form_Captcha_(.*)", $key, $regs)) {
                if ($regs[1] == $id) {
                    return array(
                        'id'    => $id,
                        'input' => $value['word']
                    );
                }
            }
        }
    }


    public function testIndexAction()
    {
        $this->dispatch('/users/register/');

        $this->assertModule('users');
        $this->assertController('register');
        $this->assertAction('index');
    }

    /**
     * Test register user
     * 
     * @todo unable provide test without advanced mocking
     */
    public function testRegisterUser()
    {
        $this->dispatch('/users/register/');
        $this->assertQuery('form#userRegisterForm');
        $html = $this->response->getBody();

        $this->resetRequest();
        $this->resetResponse();

        // TODO:
        // 1. Change email address to config email.
        // 2. Make the address in config as stub for test emails
        $this->_fixture['captcha'] = $this->getCaptcha($html);
        
        $this->markTestIncomplete('todo mock registration form');
        
        $this->request
             ->setMethod('POST')
             ->setPost($this->_fixture);
        $this->dispatch("/users/register/");
        $this->assertRedirectTo("/");

        $user = $this->_userTable->getByLogin($this->_fixture['login']);

        $this->assertNotNull($user->id);
        
        $user->status = Model_User::STATUS_ACTIVE;
        $user->save();
        
        $auth = Model_User_Manager::authenticate(
            $this->_fixture['login'],
            $this->_fixture['password']
        );
        $this->assertTrue($auth);
            
        Model_User_Manager::logout();
                            
        $this->request
             ->setMethod('POST')
             ->setPost(array('login' => $this->_fixture['login']));
        $this->dispatch("/users/register/");
        $this->assertModule('users');
        $this->assertController('register');
        $this->assertAction('index');
        
        $user->delete();
        
    }

    public function testConfirmRegistrationAction()
    {
        $user = $this->_userTable->create($this->_fixture);
        $user->status = Model_User::STATUS_REGISTER;
        $user->hashCode = md5(uniqid());
        $user->save();

        $this->request
             ->setQuery(array('hash' => $user->hashCode))
             ->setMethod('GET'); //sets method as GET ONLY this way
        $this->dispatch('/users/register/confirm-registration/');

//        var_dump($this->getResponse()->getBody());
//        $this->assertRedirect('/users/login');
        $this->assertModule('users');
        $this->assertController('register');
        $this->assertAction('confirm-registration');
        
        $user->refresh();
        $this->assertEquals($user->status, Model_User::STATUS_ACTIVE);
        
//        $this->request
//             ->setQuery(array('hash' => 134468431))
//             ->setMethod('GET'); //sets method as GET ONLY this way
//        $this->dispatch('/users/register/confirm-registration/');
//        
//        $this->assertModule('users');
//        $this->assertController('register');
//        $this->assertAction('confirm-registration');
        
        $user->delete();
    }

    /**
     * Test forget password
     *
     */
    public function testForgetPasswordAction()
    {
        Model_User_Manager::logout();
        
        $user = $this->_userTable->create($this->_fixture);
        $user->save();
        
        $this->dispatch('/users/register/forget-password');
        
        $this->assertModule('users');
        $this->assertController('register');
        $this->assertAction('forget-password');
        $this->assertQuery('form#userForgetPasswordForm');

        $this->markTestIncomplete('forget password don\'t pass this is test, hidden dependency?');
        
//        $this->request
//             ->setMethod('POST')
//             ->setPost(array('email' => $this->_fixture['email']));
//        
//        $this->dispatch('/users/register/forget-password');
        
//        $this->assertModule('users');
//        $this->assertController('register');
//        $this->assertAction('forget-password');
       
//        $this->assertRedirectTo("/login");
       
        $user->delete();
    }
   
    /**
     * @todo unable provide test without advanced mocking
     *
     */
    public function testForgetPasswordConfirmAction()
    {
        $this->markTestIncomplete('mock forget password confirm form');
    }

    /**
     * Not valid register form params:
     *  - show message
     *  - show form
     */
    public function testInvalidRegisterFormParamsShouldMessage()
    {
        $params = array('login' => "abcde'fgh");
        $this->resetRequest();
        $this->resetResponse();
        $this->request
             ->setMethod('POST')
             ->setPost($params);
        $this->dispatch("/users/register/");
        $this->assertQueryCount('form#userRegisterForm', 1);
        $this->assertQueryCount('#messages', 1);
    }
    
    /**
     * tear Down
     */
    public function tearDown()
    {
        parent::tearDown();
    }


}
