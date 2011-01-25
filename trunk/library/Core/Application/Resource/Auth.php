<?php
/**
 * Auth Resource
 * 
 * @category Core
 * @package  Core_Application
 * @subpackage Resource
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * 
 * @version  $Id: Auth.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Core_Application_Resource_Auth 
    extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Init Resource
     *
     * @return Zend_Auth
     */
    public function init()
    {
        // Save a reference to the Singleton instance of Zend_Auth
        $auth = Zend_Auth::getInstance();
        
        // Use 'Auth' instead of 'Zend_Auth'
        $auth->setStorage(new Zend_Auth_Storage_Session('Auth'));
        
        return $auth;
    }
}