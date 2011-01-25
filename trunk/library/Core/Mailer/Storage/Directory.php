<?php
/**
 * Directory adapter for Core_Mailer
 * 
 * TODO: need realization
 * 
 * @category   Core
 * @package    Core_Mailer
 * @subpackage Storage
 * 
 * @version  $Id: Directory.php 162 2010-07-12 14:58:58Z AntonShevchuk $
 */  
class Core_Mailer_Storage_Directory 
    implements Core_Mailer_Storage_Interface
{
    /**
     * Default parametrs for Mail Storage
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Constructor; 
     * Sets a values to default properties
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->_data = $data;
    }
    
    /**
     * Fetch data from any datasource 
     * 
     * Example:
     * return array(
     *          'toEmail' => '%toEmail%',
     *          'toName'  => '%toName%',
     *          'subject' => 'Hello, dear %toName%',
     *          'body'    => 'This is an activation email, for %login%;
     *           Your activation code is %code%'
     * ); 
     * @param  string $alias
     * @return array  Array of data for Core_Mailer_template object
     */
     public function getTemplate($alias)
     {
         if ( !is_null($this->_data['path']) ) {
             if ( file_exists($this->_data['path'].$alias.".xml") ) {
                 $content = simplexml_load_file(
                     $this->_data['path'] . $alias .'.xml'
                 );
                 return get_object_vars($content);
             } else {
                 return array();
             }
         } else {
             return null;
         }
     }
}
