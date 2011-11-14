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
            $this->view->headScript()
                ->appendFile($this->_normalizeUrl($asset->getJavascriptBuild()));

            $this->view->headLink()
                ->appendStylesheet($this->_normalizeUrl($asset->getStylesheetBuild()));
        } else {
            foreach ($asset->getJavascripts() as $file) {
                $this->view->headScript()->appendFile($this->_normalizeUrl($file));
            }
            foreach ($asset->getStylesheets() as $file) {
                $this->view->headLink()->appendStylesheet($this->_normalizeUrl($file));
            }
        }
    }

    /**
     * normalize url
     * reduce public path and add base url
     *
     * @param string $url
     * @return string
     */
    protected function _normalizeUrl($url)
    {
        return $this->view->baseUrl(str_replace(PUBLIC_PATH, '', $url));
    }
}
