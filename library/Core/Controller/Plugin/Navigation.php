<?php
/**
 * Controller plugin that sets the correct paths to the Zend_Layout instances
 * 
 * You can initialize this is plugin in your application.yaml
 * 
 * <code>
 * navigation:
 *   class: Core_Controller_Plugin_Navigation
 *   stackindex: 50
 *   options:
 *     config: navigation
 *     section: dashboard
 *     cache: off
 * </code>
 * 
 * @uses       Zend_Controller_Plugin_Abstract
 * 
 * @category   Core
 * @package    Core_Controller
 * @subpackage Plugins
 * 
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * 
 * @version  $Id: Navigation.php 223 2011-01-19 15:14:14Z AntonShevchuk $
 */
class Core_Controller_Plugin_Navigation extends Zend_Controller_Plugin_Abstract
{
    /**
     * Plugin configuration settings array
     *
     * @var array
     */
    protected $_options = array();

    /**
     * configuration file with settings
     *
     * @var string
     */
    protected $_config = 'navigation';

    /**
     * section
     *
     * @var string
     */
    protected $_section = 'dashboard';
        
    /**
     * cache using
     *
     * @var boolean
     */
    protected $_cache = true;

    /**
     * Constructor
     *
     * Options may include:
     * - config
     *
     * @param  Array $options
     */
    public function __construct(Array $options = array())
    {
        if (isset($options['config'])) {
            $this->_config = $options['config'];
        }

        if (isset($options['section'])) {
            $this->_section = $options['section'];
        }

        if (isset($options['cache'])) {
            $this->_cache = $options['cache'];
        }

        $this->_options = $options;
    }
    
    /**
     * preDispatch
     *
     * @param Zend_Controller_Request_Abstract $request
     */
//    public function preDispatch(Zend_Controller_Request_Abstract $request)
//    {
//        $section = $this->_getSection();
//
//        $this->_initNavigation($section);
//    }
    
    /**
     * postDispatch
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $section = $this->_getSection();
        
        if ($section == $this->_section) {
            $this->_initNavigation();
        }
    }
    
    /**
     * _initNavigation
     * 
     * @param   string $section
     * @return  void  
     */
    protected function _initNavigation($section = null) 
    {
        $container = new Zend_Navigation($this->_getConfig($section));

        $navigation = Zend_Layout::getMvcInstance()->getView()
                                                   ->navigation($container);

        if (Zend_Registry::isRegistered('Zend_Translate')) {
            $navigation->setTranslator(Zend_Registry::get('Zend_Translate'));
        }

        try {
            $acl = Zend_Registry::get('Acl');
 
            $identity = Zend_Auth::getInstance()->getIdentity();
            
            if ($identity) {
                $role = $identity->role;
            } else {
                $role = 'guest';
            }
                
            $navigation->setAcl($acl)->setRole($role);
        } catch (Exception $e) {
            // Acl is not inited            
        }
    }

    /**
     * get section
     *
     * @return string
     */
    protected function _getSection()
    {
        // Getting layout name from Core_Controller_Plugin_Layout,
        // extracting layout type (admin|default) and setting it as $this->_section
        $currentLayout = Zend_Layout::getMvcInstance()->getLayout();

        $currentLayout = preg_split('/\//', $currentLayout);

        if (isset($currentLayout[0])) {
            $section = $currentLayout[0];
        } else {
            $section = $this->_section;
        }

        return $section;
    }

    /**
     * get config
     *
     * @param  string $section
     * @return array
     */
    protected function _getConfig($section = null)
    {
        return Core_Module_Config::getConfig(
            $this->_config,
            $section,
            Core_Module_Config::MAIN_ORDER_FIRST,
            $this->_cache
        );
    }
}