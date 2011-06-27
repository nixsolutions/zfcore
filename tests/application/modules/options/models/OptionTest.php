<?php
/**
 * PagesTest
 *
 * @category Tests
 * @package  Model
 */
class Model_OptionTest extends ControllerTestCase
{
    protected $_key = null;
    protected $_object  = null;
    protected $_array   = null;
    protected $_string  = null;
    protected $_integer = null;
    protected $_float   = null;
    
    /**
     * Setup TestCase
     */
    public function setUp()
    {
        $this->_key = 'test'.date('Y-m-d');
        
        
        $this->_object = new stdClass();
        $this->_object -> qwe = date('Y-m-d');
        $this->_object -> asd = date('Y-m-d'); 
        $this->_object -> zxc = date('Y-m-d');
        
        $this->_array = array(
           "qwe" => date('Y-m-d'),
           "asd" => date('Y-m-d'),
           "zxc" => date('Y-m-d'),        
        );
        
        $this->_string  = 'Lorem Ipsum...';
        
        $this->_integer = rand(1, 1024);
        
        $this->_float = rand(1, 1024)/10000;
        
        parent::setUp();
    }
    
    /**
     * Set/Get Options from Db
     */
    function testOptionSetObject()
    {
       Options_Model_Options_Manager::set($this->_key, $this->_object);
       Options_Model_Options_Manager::clearCache($this->_key);
       
       $result = Options_Model_Options_Manager::get($this->_key);
       
       $this->assertTrue(is_object($result));
       $this->assertEquals($result, $this->_object);
    }
    
    function testOptionSetArray()
    {
       Options_Model_Options_Manager::set($this->_key, $this->_array);
       Options_Model_Options_Manager::clearCache($this->_key);
       
       $result = Options_Model_Options_Manager::get($this->_key);
       
       $this->assertTrue(is_array($result));
       $this->assertEquals($result, $this->_array);
    }

    function testOptionSetString()
    {
       Options_Model_Options_Manager::set($this->_key, $this->_string);
       Options_Model_Options_Manager::clearCache($this->_key);
       
       $result = Options_Model_Options_Manager::get($this->_key);
       
       $this->assertTrue(is_string($result));
       $this->assertEquals($result, $this->_string);
    }
    
    function testOptionSetInteger()
    {
       Options_Model_Options_Manager::set($this->_key, $this->_integer);
       Options_Model_Options_Manager::clearCache($this->_key);
       
       $result = Options_Model_Options_Manager::get($this->_key);
       
       $this->assertTrue(is_integer($result));
       $this->assertEquals($result, $this->_integer);
    }
    
    function testOptionSetFloat()
    {
       Options_Model_Options_Manager::set($this->_key, $this->_float);
       Options_Model_Options_Manager::clearCache($this->_key);
       
       $result = Options_Model_Options_Manager::get($this->_key);
       
       $this->assertTrue(is_float($result));
       $this->assertEquals($result, $this->_float);
    }
    
    function testOptionGetNotExists()
    {
       $result = Options_Model_Options_Manager::get('test'.date('Y-m-d').'-'.rand(1, 10));
       
       $this->assertTrue(is_null($result));
    }
    
    
    function testOptionDelete()
    {
       Options_Model_Options_Manager::delete($this->_key);
       $result = Options_Model_Options_Manager::get($this->_key);
       $this->assertEquals($result, null);
    }

    /**
     * Set/Get Options Namespace from Db
     */
    function testNamespaceSet()
    {
        Options_Model_Options_Manager::setNamespace(__CLASS__, array($this->_key => $this->_object));
        Options_Model_Options_Manager::clearCache(null, __CLASS__);
        
        $result = Options_Model_Options_Manager::getNamespace(__CLASS__);
        $this->assertEquals($result, array($this->_key => $this->_object));
    }
    
    function testNamespaceGetNotExists()
    {
        $result = Options_Model_Options_Manager::getNamespace(__METHOD__);
        $this->assertEquals($result, array());
    }
    
    
    function testNamespaceDelete()
    {
       Options_Model_Options_Manager::deleteNamespace(__CLASS__);
       
       $result = Options_Model_Options_Manager::get($this->_key, __CLASS__);
       $this->assertEquals($result, null);
    }
}
