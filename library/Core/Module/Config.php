<?php
/**
 * Load configuration from every module, merge it, save to cache
 * 
 * <code>
 * Core_Module_Config::getConfig(
 *     String $config,
 *     String $section,
 *     Integer Core_Module_Config::MAIN_ORDER_FIRST,
 *     Bool $cache
 * );
 * </code>
 * 
 * @category   Core
 * @package    Core_Module
 * @subpackage Config
 * 
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * 
 * @version  $Id: Config.php 223 2011-01-19 15:14:14Z AntonShevchuk $
 */
class Core_Module_Config
{

    const MAIN_ORDER_FIRST = 1;
    const MAIN_ORDER_LAST  = 2;

    /**
     * Singleton instance
     *
     * @var Core_Cache_Config
     */
    private static $_instance = null;
    
    /**
     * Array of configs
     *
     * @var array
     */
    private $_configs = array();
    
    /**
     * Path to application configs
     *
     * @var string
     */
    private $_configsDir;
    
    /**
     * Array of available modules
     *
     * @var array
     */
    private $_modules = array();
    
    /**
     * Path to modules directory
     *
     * @var string
     */
    private $_modulesDir;
    
    
    /**
     * Construct
     * 
     * @return Core_Module_Config
     */
    public function __construct() 
    {
        $this->_modules    = array_keys(Zend_Controller_Front::getInstance()->getControllerDirectory());
        $this->_modulesDir = dirname(Zend_Controller_Front::getInstance()->getModuleDirectory());
        $this->_configsDir = APPLICATION_PATH . '/configs/';
    }
    
    /**
     * Return singleton instance
     *
     * @return Core_Module_Config
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * _getCache
     *
     * @return  array
     */
    protected function _getCache() 
    {
        $frontendOptions = array("lifetime" => 3600,
                                 "automatic_serialization" => true,
                                 "automatic_cleaning_factor" => 1,
                                 "ignore_user_abort" => true);
                                 
        $backendOptions  = array("file_name_prefix" => APPLICATION_ENV . "_config",
                                 "cache_dir" =>  APPLICATION_PATH ."/../data/cache",
                                 "cache_file_umask" => 0644);
        
        // getting a Zend_Cache_Core object
        return Zend_Cache::factory(
            'Core',
            'File',
            $frontendOptions,
            $backendOptions
        );
    }
    
    /**
     * getConfig
     *
     * return configs from all modules merged with main
     *
     * @todo    add new parameter for set order of load main config
     * 
     * @param   string $filename  Configuration file name w/out extension
     * @param   string $section   Section name
     * @param   int    $order     Order of load main config
     * @param   bool   $cache     Use cache
     * @return  array  $result
     */
    public static function getConfig($filename,
                                     $section = null,
                                     $order = Core_Module_Config::MAIN_ORDER_FIRST,
                                     $cache = true)
    {
        $moduleConfig = Core_Module_Config::getInstance();
        
        if ($cache) {
            $cache = $moduleConfig->_getCache();
            
            if (!$result = $cache->load($filename.$section)) {
                $result = $moduleConfig->_getYamlConfig($filename, $section, $order);
                $cache->save($result, $filename.$section);
            }
        } else {
            $result = $moduleConfig->_getYamlConfig($filename, $section, $order);
        }
        return $result;
    }

    /**
     * _getYamlConfig
     *
     * return configs from all modules merged with main
     *
     * @todo    add new parameter for set order of load main config
     *
     * @param   string $filename  Configuration file name w/out extension
     * @param   string $section   Section name
     * @param   string $order     Order of load main config
     * @return  array  $result
     */
    protected function _getYamlConfig($filename,
                                         $section = null,
                                         $order = Core_Module_Config::MAIN_ORDER_FIRST)
    {
        $result = array();

        // load modules configuration
        foreach ($this->_modules as $module) {

            $dirPath = $this->_modulesDir . DIRECTORY_SEPARATOR .
                       $module . DIRECTORY_SEPARATOR . 'configs';

            $confFile = $dirPath . DIRECTORY_SEPARATOR . $filename .'.yaml';

            if (is_dir($dirPath) && file_exists($confFile)) {
                try {
                    $config = new Core_Config_Yaml($confFile, $section, array('ignore_definitions' => true,
                                                                              'ignore_constants' => true,
                                                                              'skip_extends' => true));
                    if ($config = $config->toArray()) {
                        $result = array_merge_recursive($result, $config);
                    }
                } catch (Zend_Config_Exception $e) {
                    // nothing, "section" is not required
                } catch (Exception $e) {
                    throw new Core_Exception($e->getMessage());
                }
            }
        }

        // load main configuration
        $mainConfig = $this->_configsDir . DIRECTORY_SEPARATOR . $filename .'.yaml';

        if (is_dir($this->_configsDir)
            && file_exists($mainConfig)) {
            $config = new Core_Config_Yaml($mainConfig, $section, array('ignore_definitions' => true,
                                                                        'ignore_constants' => true,
                                                                        'skip_extends' => true));
            if ($config = $config->toArray()) {
                if ($order === Core_Module_Config::MAIN_ORDER_FIRST ) {
                    $result = array_merge_recursive($config, $result);
                } else {
                    $result = array_merge_recursive($result, $config);
                }
            }
        }
        return $result;
    }
}