<?php

/**
 * ACL Resource
 * 
 * @category Tests
 * @package  Core
 */
class Core_Mailer_Storage_DirectoryTest extends ControllerTestCase
{
    /**
     * @access public
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->_tempDir  = APPLICATION_PATH.'/../data/templatemail/';
        
        $this->_tempFile = 'test';
        
        is_dir($this->_tempDir) or mkdir($this->_tempDir);
        
        $this->_adapter = array(
            'storage'    => array('type'    => 'Directory',
                                  'options' => array(
                                      'path'      => $this->_tempDir
                                 )),
            'transport' => array('type'    => 'ZendMail',
                                 'options' => array(
                                    'transport' => array(
                                            'class'=>'Zend_Mail_Transport_Sendmail'
                                        )
                                 )),
             );
        $this->_fixture = array( 'toEmail' => 'secunda@nixsolutions.com',
                                 'toName'  => 'Snesar Alexandr',
                                 'subject' => 'Test',
                                 'body'    => 'Test Mailer'
                                );  
        $this->_createData();
        
        Core_Mailer::init($this->_adapter);
    }
    
    /**
     * Create XML files for testing
     *
     */
    private function _createData()
    {
        $dom = new DOMDocument("1.0");
        $root = $dom->createElement('root');

        $to = $dom->createElement('toEmail');
        $toT = $dom->createTextNode($this->_fixture['toEmail']);
        $to->appendChild($toT);
        $root->appendChild($to);

        $toName = $dom->createElement('toName');
        $toNameT = $dom->createTextNode($this->_fixture['toName']);
        $toName->appendChild($toNameT);
        $root->appendChild($toName);

        $subject = $dom->createElement('subject');
        $subjectT = $dom->createTextNode($this->_fixture['subject']);
        $subject->appendChild($subjectT);
        $root->appendChild($subject);

        $body = $dom->createElement('body');
        $bodyT = $dom->createTextNode($this->_fixture['body']);
        $body->appendChild($bodyT);
        $root->appendChild($body);

        $dom->appendChild($root);
           $dom->save($this->_tempDir.$this->_tempFile.'.xml');
    }
    
    /**
     * Test getTemplate method
     *
     */
    public function testGetTemplate() 
    {
        $template = Core_Mailer::getTemplate($this->_tempFile);

        $this->assertEquals($this->_fixture['toEmail'], $template->toEmail);
        $this->assertEquals($this->_fixture['toName'], $template->toName);
        $this->assertEquals($this->_fixture['subject'], $template->subject);
        $this->assertEquals($this->_fixture['body'], $template->body);
    }
    
    /**
     * @access public
     */
    public function tearDown()
    {
        unlink($this->_tempDir.$this->_tempFile.'.xml');
        
        rmdir($this->_tempDir);
        
        parent::tearDown();
    }
}