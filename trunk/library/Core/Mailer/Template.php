<?php
/**
 * Base Template for Core_Mailer
 * 
 * @category Core
 * @package  Core_Mailer
 * 
 * @version  $Id: Template.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Core_Mailer_Template
{
    
    /**
     * Default values for mail template
     * 
     * @var array
     */
    private $_data = array(
        'toEmail' => null,
        'toName' => null,       
        'subject' => null,
        'body' => null,
        'altBody' => null,
        'mime' => null
    );
   
    /**
     * Constructor; 
     * Sets a values to default properties
     * 
     * @param array $data    
     */    
    public function __construct($data)
    {
        if ( is_array($data) ) {
            $this->setData($data);
        }
    }
    
    /**
     * setData
     *
     * setup template data from array
     *
     * @param   array     $data  
     * @return  Core_Mailer_Template
     */
    public function setData(array $data) 
    {
        foreach ($data as $key => $value) {
            $this->_data[$key] = $value;
        }
        return $this;
    }
    
    /**
     * Assign data to template
     * 
     * @param string $name
     * @param string $value
     * @return boolean
     */
    function assign($name, $value)
    {        
        $this->_data = str_replace("%".$name."%", $value, $this->_data);   
    }
    
    /**
     * Magic method for autoget values from templates
     * 
     * @param string $key
     * @return string | bool   
     */
    function __get($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];    
        }
        return false;
    }
    
    /**
     * Magic method for autoset values to templates
     * 
     * @param string $key
     * @param string $value
     * @return null
     */
    function __set($key, $value)
    {
        if (isset($key, $this->_data)) {
            $this->_data[$key] = $value;
        }
    }
    
}
