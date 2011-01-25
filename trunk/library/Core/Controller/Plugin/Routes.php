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
class Core_Controller_Plugin_Routes extends Zend_Controller_Plugin_Abstract
{
    /**
     * Plugin configuration settings array
     *
     * @var array
     */
    protected $_options = array();
    
    /**
     * Plugin configuration settings array
     *
     * @var array
     */
    protected $_config = 'routes';

    /**
     * cache using
     *
     * @var boolean
     */
    protected $_cache = true;
    
    /**
     * @var Zend_Controller_Router_Rewrite
     */
    protected $_router;

    /**
     * Config object
     *
     * @var Zend_Config
     */
    protected $_configObject;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(Array $options)
    {

        if (isset($options['config'])) {
            $this->_config = $options['config'];
        }
       
        if (isset($options['cache'])) {
            $this->_cache = $options['cache'];
        }

        $this->_options = $options;
    }

    /**
     * Route startup handler
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->setRouter();
    }

    /**
     * Route config sets to router here (from cache or scratch)
     */
    public function setRouter()
    {
        $this->_router = Zend_Controller_Front::getInstance()->getRouter();
        $this->_router->addConfig($this->_getConfig());
    }

    /**
     * get config
     *
     * @return array
     */
    protected function _getConfig()
    {
        if (!$this->_configObject) {
            $this->_configObject = new Zend_Config(
                Core_Module_Config::getConfig(
                    $this->_config,
                    null,
                    Core_Module_Config::MAIN_ORDER_LAST,
                    $this->_cache
                )
            );
        }
        return $this->_configObject;
    }
}

