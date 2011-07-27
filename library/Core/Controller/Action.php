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
}