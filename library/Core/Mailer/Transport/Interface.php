<?php 
/**
 * Interface for Core_Mailer_Mail adapters
 * 
 * @category   Core
 * @package    Core_Mailer
 * @subpackage Transport
 * 
 * @version  $Id: Interface.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
interface Core_Mailer_Transport_Interface
{
    /**
     * init mail instance and set options
     *
     * @param array $options
     */
    function __construct(array $options);
    
    /**
     * Send template
     *
     * @param Core_Mailer_Template $template
     */
    function send(Core_Mailer_Template $template);    
}