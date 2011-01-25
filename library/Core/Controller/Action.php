<?php
/**
 * Class Core_Controller_Action
 *
 * Controller class for our application
 *
 * @uses     Core_Controller_Action
 * 
 * @category Core
 * @package  Core_Controller
 * 
 * @version  $Id: Action.php 170 2010-07-26 10:56:18Z AntonShevchuk $
 */
abstract class Core_Controller_Action extends Zend_Controller_Action
{
    /**
     * Init controller plugins
     *
     */
    public function init()
    {
        /* Initialize action controller here */
    }
    
    /**
     * _isDashboard
     *
     * set requried options for Dashboard controllers
     *
     * @return  Core_Controller_Action
     */
    protected function _isDashboard() 
    {
        // change layout
        $this->_helper->layout->setLayout('dashboard/layout');
        
        // init Dojo Toolkit
        $this->view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
        
        return $this;
    }

    /**
     * Call Trnslate instance
     *
     * @param  string $message
     * @return string
     */
     protected function __($message)
     {
         if (Zend_Registry::isRegistered('Zend_Translate')) {
             return Zend_Registry::get('Zend_Translate')->_($message);
         } else {
             return $message;
         }
     }
}