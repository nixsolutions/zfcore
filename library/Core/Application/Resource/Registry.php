<?php
/**
 * Registry Resource
 *
 * <code>
 * ; example of application.ini
 * ; init registry
 * resources.registry = true
 * ; or set some variables with autoinit
 * resources.registry.var = "value"
 * </code>
 *
 * <code>
 * // get application config in your application
 * Zend_Registry::get('Application_Config');
 * </code>
 *
 * @category Core
 * @package  Core_Application
 * @subpackage Resource
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 *
 * @version  $Id: Registry.php 163 2010-07-12 16:30:02Z AntonShevchuk $
 */
class Core_Application_Resource_Registry
    extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Init Resource
     */
    public function init()
    {
        $registry = Zend_Registry::getInstance();

        // set custom
        foreach ((array)$this->getOptions() as $key => $value) {
            $registry->set($key, $value);
        }
    }
}