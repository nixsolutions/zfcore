<?php

/**
 * prepend assets
 *
 * @category Core
 * @package  Core_View
 * @subpackage Helper
 */
class Core_View_Helper_Asset extends Zend_View_Helper_Abstract
{
    /**
     * prepend assets
     *
     * @throws Core_Exception
     * @param string $package
     * @return void
     */
    public function asset($package)
    {
        if ($assets = Zend_Registry::get('assets')) {
            if (empty($assets[$package])) {
                throw new Core_Exception('"' . $package . '" not found');
            }

            /** @var $asset Core_Asset */
            $asset = $assets[$package];

            if (APPLICATION_ENV == 'production') {
                $this->view->headScript()
                    ->prependFile($this->_normalizeUrl($asset->getJavascriptBuild()));

                $this->view->headLink()
                    ->prependStylesheet($this->_normalizeUrl($asset->getStylesheetBuild()));
            } else {
                foreach ($asset->getJavascripts() as $file) {
                    $this->view->headScript()->prependFile($this->_normalizeUrl($file));
                }
                foreach ($asset->getStylesheets() as $file) {
                    $this->view->headLink()->prependStylesheet($this->_normalizeUrl($file));
                }
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
