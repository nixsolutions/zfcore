<?php

/**
 * append assets
 *
 * @category Core
 * @package  Core_View
 * @subpackage Helper
 */
class Core_View_Helper_Asset extends Zend_View_Helper_Abstract
{
    /**
     * append assets
     *
     * @return void
     */
    public function asset()
    {
        /** @var $asset Core_Asset */
        $asset = Zend_Registry::get('Core_Asset');

        if (APPLICATION_ENV == 'production') {
            $this->view->headScript()->appendFile($this->view->baseUrl($asset->getJavascriptBuild()));
            $this->view->headLink()->appendStylesheet($this->view->baseUrl($asset->getStylesheetBuild()));
        } else {
            foreach ($asset->getJavascripts() as $file) {
                $url = str_replace(PUBLIC_PATH, '', $file);
                $this->view->headScript()->appendFile($this->view->baseUrl($url));
            }
            foreach ($asset->getStylesheets() as $file) {
                $url = str_replace(PUBLIC_PATH, '', $file);
                $this->view->headLink()->appendStylesheet($this->view->baseUrl($url));
            }
        }
    }
}
