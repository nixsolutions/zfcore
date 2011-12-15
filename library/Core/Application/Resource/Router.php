<?php
/**
 * Controller plugin intended to set additional routes rules from standard
 * config file and files from modules /configs directories
 *
 * !!!ATTENTION
 *
 * As chains have to be after all the routes declarations, the application
 * config file (which better to use for chains) plugged in as last file.
 * Otherwise specify chains in the corresponding module config files
 * after the routes intended for chain have been declarated.
 *
 *
 * @uses       Zend_Controller_Plugin_Abstract
 *
 * @category   Core
 * @package    Core_Controller
 * @subpackage Plugins
 *
 * @author MYem (max.yemets@gmail.com)
 */
class Core_Application_Resource_Router
    extends Zend_Application_Resource_Router
{
    /**
     * Plugin configuration settings array
     *
     * @var array
     */
    protected $_config = 'routes';

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Controller_Router_Rewrite
     */
    public function init()
    {
        if (null === $this->_router) {
            $this->getRouter();

            $this->getBootstrap()->bootstrap('modules');

            $this->_router = Zend_Controller_Front::getInstance()->getRouter();
            $this->_router->addConfig($this->_getConfig());
        }
        return $this->_router;
    }

    /**
     * get config
     *
     * @return array
     */
    protected function _getConfig()
    {
        $config = empty($this->_options['config']) ? $this->_config : $this->_options['config'];

        return new Zend_Config(
            Core_Module_Config::getConfig(
                $config,
                null,
                Core_Module_Config::MAIN_ORDER_LAST,
                empty($this->_options['cache']) ? false : true
            )
        );
    }
}

