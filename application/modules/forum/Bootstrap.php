<?php
/**
 * Bootstrap Forum Module
 *
 * @category   Application
 * @package    Forum
 * @subpackage Bootstrap
 * 
 * @version  $Id: Bootstrap.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Forum_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initHelpers()
    {   
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        
        $viewRenderer->view->addHelperPath(
            APPLICATION_PATH . "/modules/comments/views/helpers", 
            'Comments_View_Helper'
        );
    }
}
