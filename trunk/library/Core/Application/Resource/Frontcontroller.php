<?php
/**
 * Front Controller Application Resources
 *
 * You can use this is resource in your application.ini
 * <code>
 * ; set classname
 * resources.frontController.plugins.acl.class = "Core_Controller_Plugin_Acl"
 * resources.frontController.plugins.acl.stackindex = 0
 * ; set some options
 * resources.frontController.plugins.acl.options.denied.controller = error
 * resources.frontController.plugins.acl.options.denied.action = denied
 * resources.frontController.plugins.acl.options.role = guest
 * </code>
 *
 * @category   Core
 * @package    Core_Application
 * @subpackage Resource
 * 
 * @version  $Id: Frontcontroller.php 160 2010-07-12 10:47:54Z AntonShevchuk $
 */
class Core_Application_Resource_Frontcontroller 
    extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    /**
     * Initialize Front Controller
     * 
     * @return Zend_Controller_Front
     */
    public function init()
    {
        $front = $this->getFrontController();
        
        foreach ($this->getOptions() as $key => $value) {
            switch (strtolower($key)) {
                case 'controllerdirectory':
                    if (is_string($value)) {
                        $front->setControllerDirectory($value);
                    } elseif (is_array($value)) {
                        foreach ($value as $module => $directory) {
                            $front->addControllerDirectory(
                                $directory,
                                $module
                            );
                        }
                    }
                    break;
                    
                case 'modulecontrollerdirectoryname':
                    $front->setModuleControllerDirectoryName($value);
                    break;
                    
                case 'moduledirectory':
                    $front->addModuleDirectory($value);
                    break;
                    
                case 'defaultcontrollername':
                    $front->setDefaultControllerName($value);
                    break;
                    
                case 'defaultaction':
                    $front->setDefaultAction($value);
                    break;
                    
                case 'defaultmodule':
                    $front->setDefaultModule($value);
                    break;
                    
                case 'baseurl':
                    if (!empty($value)) {
                        $front->setBaseUrl($value);
                    }
                    break;
                    
                case 'params':
                    $front->setParams($value);
                    break;
                    
                case 'plugins':
                    foreach ((array) $value as $pluginClass) {
                        $stackIndex = null;
                        $options    = array();
                        if (is_array($pluginClass)) {
                            $pluginClass = array_change_key_case($pluginClass, CASE_LOWER);
                            if (isset($pluginClass['class'])) {
                                if (isset($pluginClass['stackindex'])) {
                                    $stackIndex = $pluginClass['stackindex'];
                                }
                                if (isset($pluginClass['options'])) {
                                    $options = $pluginClass['options'];
                                }
                                $pluginClass = $pluginClass['class'];
                            }
                        }
                        $plugin = new $pluginClass($options);
                        $front->registerPlugin($plugin, $stackIndex);
                    }
                    break;

                case 'returnresponse':
                    $front->returnResponse((bool) $value);
                    break;

                case 'throwexceptions':
                    $front->throwExceptions((bool) $value);
                    break;

                case 'actionhelperpaths':
                    if (is_array($value)) {
                        foreach ($value as $helperPrefix => $helperPath) {
                            Zend_Controller_Action_HelperBroker::addPath($helperPath, $helperPrefix);
                        }
                    }
                    break;

                default:
                    $front->setParam($key, $value);
                    break;
            }
        }

        if (null !== ($bootstrap = $this->getBootstrap())) {
            $this->getBootstrap()->frontController = $front;
        }

        return $front;
    }

    /**
     * Retrieve front controller instance
     * 
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        if (null === $this->_front) {
            $this->_front = Zend_Controller_Front::getInstance();
        }
        return $this->_front;
    }
}
