<?php
/**
 * Copyright (c) 2012 by PHP Team of NIX Solutions Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

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
 *
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link       http://anton.shevchuk.name
 */
class Core_Module_Config
{

    const MAIN_ORDER_FIRST = 1;
    const MAIN_ORDER_LAST = 2;

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
        $this->_modules = array_keys(Zend_Controller_Front::getInstance()->getControllerDirectory());
        $this->_modulesDir = dirname(Zend_Controller_Front::getInstance()->getModuleDirectory());
        $this->_configsDir = APPLICATION_PATH . '/configs/';

        sort($this->_modules);
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
     * getConfig
     *
     * return configs from all modules merged with main
     *
     * @todo    add new parameter for set order of load main config
     *
     * @param   string $filename  Configuration file name w/out extension
     * @param   string $section   Section name
     * @param   int    $order     Order of load main config
     * @param   string $cache     cache name
     * @return  array  $result
     */
    public static function getConfig(
        $filename,
        $section = null,
        $order = Core_Module_Config::MAIN_ORDER_FIRST,
        $cache = null
    )
    {
        $moduleConfig = Core_Module_Config::getInstance();

        if ($cache) {
            if (!$cache instanceof Zend_Cache_Core) {
                $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');

                if ($bootstrap && $bootstrap->hasPluginResource('CacheManager')) {
                    $manager = $bootstrap->getResource('CacheManager');
                    $cache = $manager->getCache($cache);
                } else {
                    $cache = null;
                }
            }
        }
        if ($cache) {
            if (!$result = $cache->load($filename . $section)) {
                $result = $moduleConfig->_getYamlConfig($filename, $section, $order);
                $cache->save($result, $filename . $section);
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
    protected function _getYamlConfig(
        $filename,
        $section = null,
        $order = Core_Module_Config::MAIN_ORDER_FIRST
    )
    {
        $result = array();

        // load modules configuration
        foreach ($this->_modules as $module) {

            $dirPath = $this->_modulesDir . DIRECTORY_SEPARATOR .
                $module . DIRECTORY_SEPARATOR . 'configs';

            $confFile = $dirPath . DIRECTORY_SEPARATOR . $filename . '.yaml';

            if (is_dir($dirPath) && file_exists($confFile)) {
                try {
                    $config = new Core_Config_Yaml($confFile, $section);
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
        $mainConfig = $this->_configsDir . DIRECTORY_SEPARATOR . $filename . '.yaml';

        if (is_dir($this->_configsDir)
            && file_exists($mainConfig)
        ) {
            $config = new Core_Config_Yaml($mainConfig, $section);
            if ($config = $config->toArray()) {
                if ($order === Core_Module_Config::MAIN_ORDER_FIRST) {
                    $result = array_merge_recursive($config, $result);
                } else {
                    $result = array_merge_recursive($result, $config);
                }
            }
        }
        return $result;
    }
}