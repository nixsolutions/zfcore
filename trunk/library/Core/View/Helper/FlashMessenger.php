<?php
/**
 * @category   Core
 * @package    Core_View
 * @subpackage Helper
 * 
 * @version  $Id: FlashMessenger.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Core_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract
{
    /**
     * Generates a javascript
     *
     * @access public
     *
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    public function flashmessenger()
    {
        return Zend_Controller_Action_HelperBroker::getStaticHelper("FlashMessenger");
    }
}
