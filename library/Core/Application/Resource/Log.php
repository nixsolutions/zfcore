<?php
/**
 * Log Resource
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
class Core_Application_Resource_Log
    extends Zend_Application_Resource_Log
{
    /**
     * Attach logger
     *
     * @param  Zend_Log $log
     * @return Zend_Application_Resource_Log
     */
    public function setLog(Zend_Log $log)
    {
        $this->_log = $log;
        Zend_Registry::set('Log', $log);

        return $this;
    }
}