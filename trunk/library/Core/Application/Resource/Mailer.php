<?php
/**
 * Mailer Resource
 *
 * <code>
 * ; example of application.ini
 * 
 * ; set storage settings (DbTable or Directory)
 * resource.mailer.storage.type              = DbTable
 * resource.mailer.storage.options.table     = Model_DbTable_Mail
 * 
 * ; set transport settings (ZendMail or PHPMailer)
 * resource.mailer.transport.type              = ZendMail
 * resource.mailer.transport.options.transport = Zend_Mail_Transport_Sendmail
 * resource.mailer.transport.options.fromEmail = dark@nixsolutions.com
 * resource.mailer.transport.optinos.fromName  = ZFCore Webmaster
 * </code>
 * 
 * @category Core
 * @package  Core_Application
 * @subpackage Resource
 * 
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * 
 * @version  $Id: Mailer.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Core_Application_Resource_Mailer 
    extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Init Resource
     */
    public function init()
    {
        $registry = Zend_Registry::getInstance();
        $registry->set('Core_Mailer_Config', $this->getOptions());
    }
}