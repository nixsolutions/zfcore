<?php
/**
 * IndexController for admin module
 *
 * @category   Application
 * @package    Dashboard
 * @subpackage Controller
 * 
 * @version  $Id: IndexController.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Admin_IndexController extends Core_Controller_Action
{
    public function init()
    {
        /* Initialize */
        parent::init();
        
        /* is Dashboard Controller */
        $this->_isDashboard();
    }

    public function indexAction()
    {
        
    }
}
