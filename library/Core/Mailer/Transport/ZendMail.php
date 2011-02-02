<?php 
/**
 * Core_Mailer_Mail Adapter for Zend_Mail
 * 
 * @category   Core
 * @package    Core_Mailer
 * @subpackage Transport
 * 
 * @version  $Id: ZendMail.php 151 2010-07-08 09:07:58Z AntonShevchuk $
 */
class Core_Mailer_Transport_ZendMail
    implements Core_Mailer_Transport_Interface
{
    /**
     * Options for Zend_Mail
     *
     * @var array
     */
    private $_options;
    
    /**
     * Charset
     *
     * @var array
     */
    private $_charset = 'iso-8859-1';
    
    /**
     * Init Zend_Mail and set defaults
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->_options = $options;
        
        if (isset($options['charset'])) {
            $this->_charset = $options['charset'];
        }

        if (isset($options['transport'])) {
            $this->_setupTransport();
        }
        
        $this->_setupDefaults();
    }

    /**
     * setup transport
     *
     * @throws Zend_Application_Resource_Exception
     * @return Core_Mailer_Transport_ZendMail
     */
    protected function _setupTransport()
    {
        $options = $this->_options;

        $transportName = $options['transport']['class'];

        unset($options['transport']['class']);

        switch($transportName) {
            case 'Zend_Mail_Transport_Smtp':
                if (!isset($options['transport']['host'])) {
                    throw new Zend_Application_Resource_Exception(
                        'A host is necessary for smtp transport,'
                        .' but none was given');
                }
                $host = $options['transport']['host'];
                unset($options['transport']['host']);
                $transport = new Zend_Mail_Transport_Smtp($host, $options);
                break;
            case 'Zend_Mail_Transport_File':
                $transport = new Zend_Mail_Transport_File($options['transport']);
                break;
            case 'Zend_Mail_Transport_Sendmail':
            default:
                if (isset($options['transport']['params'])) {
                    $transport = new Zend_Mail_Transport_Sendmail($options['transport']['params']);
                } else {
                    $transport = new Zend_Mail_Transport_Sendmail($options['transport']['params']);
                }

                break;
        }
        Zend_Mail::setDefaultTransport($transport);
        return $this;
    }

    /**
     * setup default values
     *
     * @return Core_Mailer_Transport_ZendMail
     */
    protected function _setupDefaults()
    {
        Zend_Mail::setDefaultFrom($this->_options['fromEmail'], $this->_options['fromName']);

        if (!isset($this->_options['replyEmail'])) {
            $this->_options['replyEmail'] = $this->_options['fromEmail'];
        }

        if (!isset($this->_options['replyName'])) {
            $this->_options['replyName'] = $this->_options['fromName'];
        }

        Zend_Mail::setDefaultReplyTo($this->_options['replyEmail'], $this->_options['replyName']);
        return $this;
    }

    /**
     * Send template
     *
     * @param Core_Mailer_Template $template
     * @return bool
     */
    public function send(Core_Mailer_Template $template)
    {
        $mail = new Zend_Mail($this->_charset);

        $mail
             ->setBodyHtml($template->body)
             ->setBodyText($template->altBody)
             ->setSubject($template->subject)
             ->addTo($template->toEmail, $template->toName);
             
        // MIME
        if ($template->mime instanceof Zend_Mime_Part) {
            $mail->addAttachment($template->mime);
        }
        return $mail->send();
    }    
}