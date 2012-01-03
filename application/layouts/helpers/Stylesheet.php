<?php
/**
 * @category Application
 * @package  Core_View
 * @subpackage Helper
 *
 * @version  $Id$
 */
class Application_View_Helper_Stylesheet extends Zend_View_Helper_Abstract
{
    /**
     * Generates a html code for include stylesheet
     *
     * @access public
     * @return Application_View_Helper_Stylesheet
     */
    public function stylesheet()
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
     * @return  rettype  return
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
                $style = $this->view->baseUrl("modules/$module/css/style.css");
                break;
            case 'controller':
                $style = $this->view->baseUrl("modules/$module/css/$controller.css");
                break;
            case 'action':
                $style = $this->view->baseUrl("modules/$module/css/$controller/$action.css");
            default:
                break;
        }

        /** production enveriment? */
        if (strtolower(APPLICATION_ENV) == 'production') {
            switch ($method) {
                case 'module':
                    $styleMin = $this->view->baseUrl("modules/$module/css/style.min.css");
                    break;
                case 'controller':
                    $styleMin = $this->view->baseUrl("modules/$module/css/$controller.min.css");
                    break;
                case 'action':
                    $styleMin = $this->view->baseUrl("modules/$module/css/$controller/$action.min.css");
                default:
                    break;
            }

            /** min exists? */
            if (file_exists(realpath(APPLICATION_PATH . '/../public' . $styleMin))) {
                $this->view->headLink()->appendStylesheet($styleMin);
            } else {
                $this->view->headLink()->appendStylesheet($style);
            }
        } else {
            $this->view->headLink()->appendStylesheet($style);
        }
    }
}
