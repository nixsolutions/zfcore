<?php

class Admin_MailControllerTest extends ControllerTestCase
{

    /**
     * Set up
     *
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->_doLogin(Model_User::ROLE_ADMIN, Model_User::STATUS_ACTIVE);
        
        $this->_fixture = array(
            'alias'       => 'registration',
            'subject'     => 'hello message'.time(),
            'description' => 'hello message desc'.date('Y-m-d H:i:s'),
            'body'        => 'hello test'.time(),
            'altBody'     => 'hello test'.time(),
            'fromName'    => 'vasya',
            'fromEmail'   => 'test2@mail.ru',
            'signature'   => true);
        
        $this->_layout = 'basdfasdfasd'.time();
        
        $this->_table = new Model_Mail_Table();
    }
    
    /**
     * Test /admin/mail/create
     *
     */
    public function testCreateAction()
    {
        $this->dispatch('/admin/mail/create');
//        $this->assertRedirectTo('/admin/error/notfound');
        $this->assertModule('admin');
//        $this->assertController('error');
//        $this->assertAction('notfound');
    }
    
    /**
     * Test /admin/mail/layout
     *
     */
    public function testLayoutAction()
    {
        $this->dispatch('/admin/mail/layout');
        $this->assertModule('admin');
        $this->assertController('mail');
        $this->assertAction('layout');
        //$this->assertQuery('form#mailLayoutForm');
        
        $this->assertNotEquals($this->_layout, Model_Mail::getLayout());
        $this->request
             ->setMethod('POST')
             ->setPost(array('body' => $this->_layout));
             
        $this->dispatch('/admin/mail/layout');
        $this->assertRedirectTo('/admin/mail');
        $this->assertEquals($this->_layout, Model_Mail::getLayout());
    }
    
    /**
     * Test /admin/mail/
     *
     */
    public function testIndexAction()
    {
        $this->dispatch('/admin/mail');
        $this->assertModule('admin');
        $this->assertController('mail');
        $this->assertAction('index');
        $this->assertQuery('div#gridContainer');
    }
    
    /**
     * Test /admin/mail/delete
     *
     */
    public function testDeleteAction()
    {
        $this->dispatch('/admin/mail/delete');
        $this->assertModule('admin');
        $this->assertController('error');
        $this->assertAction('notfound');
    }
    
    /**
     * Test /admin/mail/send
     *
     */
    public function testSendAction()
    {
        $this->dispatch('/admin/mail/send');
        $this->assertModule('admin');
        $this->assertController('mail');
        $this->assertAction('send');
        //$this->assertQuery('form#mailSendForm');
        
        $this->dispatch('/admin/mail/send/alias/35sasfd2');
        $this->assertModule('admin');
        $this->assertController('error');
        $this->assertAction('internal');
        
        $this->dispatch('/admin/mail/send/alias/'.$this->_fixture['alias']);
        $this->assertModule('admin');
        $this->assertController('mail');
        $this->assertAction('send');
        //$this->assertQuery('form#mailSendForm');
    }
    
    /**
     * Test /admin/mail/edit
     *
     */
    public function testEditAction()
    {
        $mail = $this->_table->getByAlias($this->_fixture['alias']);
        $registration = array_merge($mail->toArray(), $this->_fixture);
        
//        $this->dispatch('/admin/mail/edit/id/' . $mail->id);
        $this->request
             ->setMethod('POST')
             ->setPost($registration);
        
        $this->dispatch('/admin/mail/edit/id/' . $mail->id);
        //$this->assertQuery('form#mailEditForm');
        $this->assertRedirectTo('/admin/mail');

        $mail = $this->_table->getByAlias($this->_fixture['alias']);
        
        $this->assertEquals($registration, $mail->toArray());
    }
    
    /**
     * Test /admin/mail/store
     *
     */
    public function testAdminMailStoreAction()
    {
        $this->request->setQuery(array('count' => 1));
        $this->dispatch('/admin/mail/store/');
         
        $result = json_decode($this->response->getBody(), true);
        $this->assertEquals(1, count($result['items']));
        
        $this->request->setQuery(array('count' => 2));
        $this->dispatch('/admin/mail/store/');
         
        $result = json_decode($this->response->getBody(), true);
        $this->assertEquals(2, count($result['items']));
    }
    
    /**
     * tear Down
     *
     */
    public function tearDown()
    {
        parent::tearDown();
    }


}

