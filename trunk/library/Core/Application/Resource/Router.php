<?php
/**
 * Resource for initializing the routers
 *
 * @category Core
 * @package  Core_Application
 * @subpackage Resource
 * 
 * @version  $Id: Router.php 209 2010-11-05 14:44:58Z AntonShevchuk $
 */
class Core_Application_Resource_Router
    extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Zend_Controller_Router_Rewrite
     */
    protected $_router;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Controller_Router_Rewrite
     */
    public function init()
    {
        return $this->getRouter();
    }

    /**
     * Retrieve router object
     *
     * @return Zend_Controller_Router_Rewrite
     */
    public function getRouter()
    {
        if (null === $this->_router) {
            
            $options = $this->getOptions();
            if ($options['cache']) {
                $this->_initCache($options['config']);
            } else {
                $this->_setRouter($options['config']);
            }
        }
        return $this->_router;
    }
    
    /**
     * Get config
     *
     * @param string $config
     */
    public function _setRouter($config)
    {
        if (!is_file($config)) {
            throw new Exception('Router config not found in '. $config);
        }
        
        //$config = new Core_Config_Xml($config);
        $config = new Zend_Config_Xml($config);

//        if (empty($config)) {
//            return false;
//        }

        /*
        $config = $config->toArray();
        //$config = array_merge_recursive(Core_Parser_Folder::getXmlConfig('routes'), $config);
       
        $config = new Zend_Config($config);
         * 
         */
//        var_dump($config);
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('FrontController');
        
        $this->_router = $bootstrap->getContainer()->frontcontroller
                                                   ->getRouter();
        $this->_router->addConfig($config, 'routes');
        //var_dump($this->_router);
    }
    
    /**
     * init cache
     *
     */
    private function _initCache($config)
    {
        // We set an array of options for the choosen frontend
        $frontendOptions = array('automatic_serialization' => true);
        
        // We set an array of options for the choosen backend
        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH .'/../data/cache',
            'file_name_prefix' => 'router');
        
        // We create an instance of Zend_Cache
        // (of course, the two last arguments are optional)
        $cache = Zend_Cache::factory(
            'Core',
            'File',
            $frontendOptions,
            $backendOptions
        );

        if (!($this->_router = $cache->load('router'))) {
            // cache miss
            $this->_setRouter($config);
            $cache->save($this->_router);
        } else {
            $bootstrap = $this->getBootstrap();
            $bootstrap->bootstrap('FrontController');
            $bootstrap->getContainer()->frontcontroller
                                      ->setRouter($this->_router);
        }
    }
}
