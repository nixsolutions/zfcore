<?php
/**
 *
 */
/**
 * AboutController for admin module
 *
 * @category   Application
 * @package    Dashboard
 * @subpackage Controller
 */
class Admin_AboutController extends Core_Controller_Action
{
    /**
     * Initialize Controller
     *
     * @return void
     */
    public function init()
    {
        /* Initialize */
        parent::init();

        /* is Dashboard Controller */
        $this->_useDashboard();
    }

    /**
     * Need implementation
     *
     * @return void
     */
    public function indexAction()
    {
        // Need implementation
    }
}
