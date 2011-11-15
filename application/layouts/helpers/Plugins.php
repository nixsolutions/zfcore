<?php

/**
 * add some plugins
 *
 * @category Application
 * @package  Core_View
 * @subpackage Helper
 */
class Application_View_Helper_Plugins extends Zend_View_Helper_Abstract
{
    /**
     * add some plugins
     *
     * @return Application_View_Helper_Plugins
     */
    public function plugins()
    {
        return $this;
    }

    /**
     * add grid
     *
     * @return Application_View_Helper_Plugins
     */
    public function grid()
    {
        $this->view->headScript()
            ->appendFile($this->view->baseUrl('/scripts/jquery/grid/jquery.grid.js'));

        $this->view->headLink()
            ->appendStylesheet($this->view->baseUrl('/scripts/jquery/grid/grid.css'));

        return $this;
    }

    /**
     * add redactor
     *
     * @return Application_View_Helper_Plugins
     */
    public function redactor()
    {
        $this->view->headScript()
            ->appendFile($this->view->baseUrl('/scripts/jquery/redactor/redactor.js'));

        $this->view->headLink()
            ->appendStylesheet($this->view->baseUrl('/scripts/jquery/redactor/css/redactor.css'));

        return $this;
    }

    /**
     * add elfinder
     *
     * @return Application_View_Helper_Plugins
     */
    public function elfinder()
    {
        $this->view->headLink()
            ->appendStylesheet($this->view->baseUrl('scripts/jquery/elfinder/css/elfinder.full.css'))
            ->appendStylesheet($this->view->baseUrl('scripts/jquery/elfinder/css/theme.css'));

        $this->view->headScript()
            ->appendFile($this->view->baseUrl('scripts/jquery/elfinder/js/elfinder.full.js'));

        return $this;
    }
}
