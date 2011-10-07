<?php

/**
 * add grid
 *
 * @category Application
 * @package  Core_View
 * @subpackage Helper
 */
class Application_View_Helper_Grid extends Zend_View_Helper_Abstract
{
    public function grid()
    {
        $this->view->headScript()
            ->appendFile($this->view->baseUrl('/scripts/jquery/grid/jquery.grid.js'));

        $this->view->headLink()
            ->appendStylesheet($this->view->baseUrl('/scripts/jquery/grid/grid.css'));
    }
}
