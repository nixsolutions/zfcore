<?php
/**
 * Bootstrap Application
 *
 * @category Application
 * @package  Bootstrap
 * 
 * @version  $Id: Bootstrap.php 1607 2009-12-02 15:10:38Z dark $
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @return Zend_Application_Module_Autoloader 
     */
    protected function _initAutoload()
    {
//        $autoloader = Zend_Loader_Autoloader::getInstance();
//        $autoloader->registerNamespace('Core');
//        $autoloader->suppressNotFoundWarnings(false);
        $moduleLoader = new Zend_Application_Module_Autoloader(
            array(
                'namespace' => '',
                'basePath'  => APPLICATION_PATH
            )
        );
        return $moduleLoader;
    }

    /**
     * @return Bootstrap
     */
    protected function _initCache()
    {
        // Db_Table metadata cache initialiazation
        $this->bootstrap('db');

        $cacheFrontendOptions = array(
            'automatic_serialization' => true,
            'cache_id_prefix'         => 'meta',
            'lifetime'                => 3600,
        );
        $cacheBackendOptions = array(
            'cache_dir'        => APPLICATION_PATH . '/../data/cache',
            'file_name_prefix' => APPLICATION_ENV  . '_metadata',
        );
        $cache = Zend_Cache::factory(
            'Core',
            'File',
            $cacheFrontendOptions,
            $cacheBackendOptions
        );
                                     
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        
        /**  
         * set Include File Cache 
         */   
        $classFileIncCache = APPLICATION_PATH . '/../data/cache/'.APPLICATION_ENV.'_loader.php';

        if (file_exists($classFileIncCache)) include_once $classFileIncCache;

        Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
        return $this;
    }


    /**
     * 
     * @return Zend_View
     */
    protected function _initView()
    {
        $this->bootstrap('layout');
        
        $options = $this->getOption('view');
        
        // Initialize view
        $view = new Core_View();
        $view->headMeta()
             ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
             ->appendHttpEquiv('Content-Language', 'en-US');
        $view->headTitle($options['title']);
        $view->doctype($options['doctype']);

        /**
         * FIXME:
         * <code>
         *   resources.view.helperPath.Zend_Dojo_View_Helper = "Zend/Dojo/View/Helper/"
         *   resources.view.helperPath.Core_View_Helper = "Core/View/Helper"
         *   resources.view.filterPath.Core_View_Filter = "Core/View/Filter"
         * </code>
         */
        $view->addHelperPath('Core/View/Helper/', 'Core_View_Helper');        
        $view->addFilterPath('Core/View/Filter', 'Core_View_Filter');
        
        /* Application specified scripts/helpers/filters */
        $view->addScriptPath(APPLICATION_PATH . '/views/scripts');
        $view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Application_View_Helper');
        $view->addFilterPath(APPLICATION_PATH . '/views/filters', 'Application_View_Filter');

        $view->assign('env', APPLICATION_ENV);

        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);

        // Return it, so that it can be stored by the bootstrap
        return $view;
    }
}