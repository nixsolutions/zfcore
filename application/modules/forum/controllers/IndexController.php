<?php
/**
 * IndexController for forum module
 *
 * @category   Application
 * @package    Forum
 * @subpackage Controller
 *
 * @version  $Id: IndexController.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Forum_IndexController extends Core_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction()
    {
        $this->_forward('index', 'category');
    }

}
