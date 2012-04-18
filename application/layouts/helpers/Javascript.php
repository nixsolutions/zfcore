<?php
/**
 * @category Application
 * @package  Core_View
 * @subpackage Helper
 * 
 * @version  $Id$
 */
class Application_View_Helper_Javascript extends Zend_View_Helper_Abstract
{
    /**
     * Generates a html code for include js
     *
     * @access public
     * @return Application_View_Helper_Javascript
     */
    public function javascript()
    {
        return $this;
    }
    
    /**
     * __call
     *
     * append stylesheet by module/controller/action
     *
     * @param   string $method 
     * @param   mixed  $args
     * @return  void
     */
    public function __call($method, $args) 
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        
        /* @var $request Zend_Controller_Request_Abstract */
        $module     = $request->getModuleName();
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        
        // switch statement for $method
        switch ($method) {
            case 'module':
                $script = $this->view->baseUrl("modules/$module/scripts/script.js");
                break;
            case 'controller':
                $script = $this->view->baseUrl("modules/$module/scripts/$controller.js");
                break;
            case 'action':
                $script = $this->view->baseUrl("modules/$module/scripts/$controller/$action.js");
            default:
                break;
        }
        
        /** production enveriment? */
        if (strtolower(APPLICATION_ENV) == 'production') {
            switch ($method) {
                case 'module':
                    $scriptMin = $this->view->baseUrl("modules/$module/scripts/script.min.js");
                    break;
                case 'controller':
                    $scriptMin = $this->view->baseUrl("modules/$module/scripts/$controller.min.js");
                    break;
                case 'action':
                    $scriptMin = $this->view->baseUrl("modules/$module/scripts/$controller/$action.min.js");
                default:
                    break;
            }
            
            /** min exists? */
            if (file_exists(realpath(APPLICATION_PATH . '/../public' . $scriptMin))) {
                $this->view->headScript()->appendFile($scriptMin);
            } else {
                $this->view->headScript()->appendFile($script);
            }
        } else {
            $this->view->headScript()->appendFile($script);
        }
    }
}
