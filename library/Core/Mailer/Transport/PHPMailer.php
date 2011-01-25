<?php 
/**
 * Core_Mailer_Mail Adapter for PHPMailer
 * 
 * @category   Core
 * @package    Core_Mailer
 * @subpackage Transport
 * 
 * @version  $Id: PHPMailer.php 136 2010-06-16 14:30:42Z AntonShevchuk $
 */
class Core_Mailer_Transport_PHPMailer
    implements Core_Mailer_Transport_Interface
{
    /**
     * Options for Zend_Mail
     *
     * @var array
     */
    private $_options;
    
    /**
     * Init Zend_Mail and set defaults
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->_options = $options;
    }
    
    /**
     * Send template
     *
     * @param Core_Mailer_Template $template
     * @return bool
     */
    public function send(Core_Mailer_Template $template)
    {
         // TODO implement this
        $mail = new PHPMailer();
        $mail->PluginDir = $options['pluginDir'];
        $mail->IsSMTP();
        $mail->Host = $options['smtp'];
        $mail->Lang($options['lang']);

        if (isset($options['auth']['username']) &&
            isset($options['auth']['password'])) {
            $mail->SMTPAuth = true;
            $mail->Username = $options['auth']['username'];
            $mail->Password = $options['auth']['password'];
        }
    }    
}