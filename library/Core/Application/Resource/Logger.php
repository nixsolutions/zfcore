<?php
/**
 * Logger Resource
 * 
 * <code>
 * ; example of application.ini
 * ; use only one logger with default settings
 * resources.logger.file = true
 * 
 * ; or enable and set path to logs dir
 * resources.logger.writer.1.type = file
 * resources.logger.writer.1.path = APPLICATION_PATH "/../data/logs/"
 * 
 * ; firebug
 * resources.logger.writer.2.type = firebug
 * 
 * ; mail
 * resources.logger.writer.3.type = mail
 * resources.logger.writer.3.from = admin@nixsolutions.com
 * resources.logger.writer.3.to = admin@nixsolutions.com
 * resources.logger.writer.3.subject = "Logger %s"
 * 
 * ; to null
 * resources.logger.writer.0.type = null
 * </code>
 * 
 * @category Core
 * @package  Core_Application
 * @subpackage Resource
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * 
 * @version  $Id: Logger.php 163 2010-07-12 16:30:02Z AntonShevchuk $
 */
class Core_Application_Resource_Logger 
    extends Zend_Application_Resource_ResourceAbstract
{
    /**
     *
     * @var Zend_Log
     */
    protected $_logger;

    /**
     * Init Resource
     * 
     * @return Zend_Log_Writer_Abstract
     */
    public function init()
    {
        // Return view so bootstrap will store it in the registry
        return $this->getLogger();
    }

    /**
     * Init log writers
     * 
     * @return Zend_Log_Writer_Abstract
     */
    public function getLogger()
    {
        if (null === $this->_logger) {
            $options = $this->getOptions();
            $this->_logger = new Zend_Log();
            
            
            if (isset($options['writer'])) {
                foreach ($options['writer'] as $writer) {
                    // switch statement for $writer['type']
                    switch ($writer['type']) {
                        case 'db':
                            $db = $writer['db'];
                            $table = $writer['table'];
                            $columnMap = $writer['columnMap'];
                            $writer = new Zend_Log_Writer_Db($db, 
                                                             $table, 
                                                             $columnMap);
                            break;
                        case 'file':
                            $path = isset($writer['path'])?$writer['path']:APPLICATION_PATH . '/../data/logs/';
                            $mode = isset($writer['mode'])?$writer['mode']:'a';
                            $writer = new Zend_Log_Writer_Stream($path.APPLICATION_ENV.'_'.date('Y.m.d').'.log',
                                                                 $mode);
                            break;
                        case 'mock':
                            $writer = new Zend_Log_Writer_Mock();
                        case 'mail':
                            $mail = new Zend_Mail();
                            $mail->setFrom($writer['from'])
                                 ->addTo($writer['to'])
                                 ->setSubject(sprintf($writer['subject'], date('Y-m-d H:i')));
                            $writer = new Zend_Log_Writer_Mail($mail);
                            break;                    
                        case 'firebug':
                            $writer = new Zend_Log_Writer_Firebug();
                            break;
                        case 'null':
                        default:
                            $writer = new Zend_Log_Writer_Null();
                            break;
                    }
                    $this->_logger->addWriter($writer);
                }
            }
            
            Zend_Registry::set('Log', $this->_logger);
                
        }
        return $this->_logger;
    }
}