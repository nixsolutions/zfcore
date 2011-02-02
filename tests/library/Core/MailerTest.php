<?php

/**
 * ACL Resource
 * 
 * @category Tests
 * @package  Core
 */
class Core_MailerTest extends ControllerTestCase
{
    /**
     * @access protected
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->_fixture = array(
            'storage'   => array('type'    => 'DbTable',
                                 'options' => array(
                                    'table'  => 'Model_Mail_Table'
                                 )),
            'transport' => array('type'    => 'ZendMail',
                                 'options' => array(
                                    'transport' => array(
                                            'class'=>'Zend_Mail_Transport_Sendmail'
                                        )
                                 )),
                               );
    }
   
    public function testInitFail()
    {
        Zend_Registry::set('Core_Mailer_Config', null);
        try {
            Core_Mailer::init();
            $this->fail('This message should not be dispayed');
        } catch (Core_Mailer_Exception $e) {
            $this->assertInternalType('string', $e->getMessage());
        }
        
        try {
            $data = array_merge(
                $this->_fixture,
                array('transport' =>
                    array('type' => 'asdsf'))
            );
            Core_Mailer::init($data);
            $this->fail('This message should not be dispayed');
        } catch (Core_Mailer_Exception $e) {
            $this->assertInternalType('string', $e->getMessage());
        }
    }
    
    public function testInitFromRegisty()
    {
        Zend_Registry::set('Core_Mailer_Config', $this->_fixture);
        
        try {
            Core_Mailer::init();
        } catch (Core_Mailer_Exception $e) {
            $this->fail($e->getMessage());
        }
        
        Zend_Registry::set(
            'Core_Mailer_Config',
            new Zend_Config($this->_fixture)
        );
        
        try {
            Core_Mailer::init();
        } catch (Core_Mailer_Exception $e) {
            $this->fail($e->getMessage());
        }
    }
    
    public function testInitFromOptions()
    {
        try {
            Core_Mailer::init($this->_fixture);
        } catch (Core_Mailer_Exception $e) {
            $this->fail($e->getMessage());
        }
        
        try {
            Core_Mailer::init(new Zend_Config($this->_fixture));
        } catch (Core_Mailer_Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}