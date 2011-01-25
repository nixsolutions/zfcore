<?php
/**
 * Core_Mailer Adapter for templates in Database
 * 
 * TODO: need realization
 * 
 * @category   Core
 * @package    Core_Mailer
 * @subpackage Storage
 * 
 * @version  $Id: DbTable.php 162 2010-07-12 14:58:58Z AntonShevchuk $
 */
class Core_Mailer_Storage_DbTable 
    implements Core_Mailer_Storage_Interface
{
    /**
     * Default parametrs for DbTable Adapter
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
     * Return array of data for template
     * 
     * return array(
     *       'toEmail' => '%to%',
     *       'toName'  => '%to_name%',
     *       'subject' => 'Hello, dear %to_name%',
     *       'body'    => 'This is an activation email, for %user_login%; 
     *       Your activation code is %user_code%'
     *   ); 
     * 
     * @param string $alias
     * @return array Fixed structure
     */
    public function getTemplate($alias)
    {
        $dbtable = $this->_data['table'];
        if ( is_object($dbtable) ) {
            $obj = $dbtable;
        } elseif ( is_string($this->_data['table']) ) {
            $obj = new $this->_data['table'];
        } else {
            return null;
        }
        if (is_object($obj) &&
            is_object($obj->getAdapter())) {
            
            $res = $obj->fetchRow(
                'alias = ' . $obj->getAdapter()->quote($alias)
            );
            return is_object($res)?$res->toArray():array();
        }
        return null;
    }
}
