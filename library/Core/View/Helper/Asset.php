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
 * prepend assets
 *
 * @category   Core
 * @package    Core_View
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
        if ($assets = Zend_Registry::get( 'assets' )) {
            if (empty($assets[$package])) {
                throw new Core_Exception('"' . $package . '" not found');
            }

            /** @var $asset Core_Asset */
            $asset = $assets[$package];

            if (APPLICATION_ENV == 'production') {
                $this->view->headScript()
                    ->prependFile( $this->_normalizeUrl( $asset->getJavascriptBuild() ) );

                $this->view->headLink()
                    ->prependStylesheet( $this->_normalizeUrl( $asset->getStylesheetBuild() ) );
            } else {
                foreach ($asset->getJavascripts() as $file) {
                    $this->view->headScript()->prependFile( $this->_normalizeUrl( $file ) );
                }
                foreach ($asset->getStylesheets() as $file) {
                    $this->view->headLink()->prependStylesheet( $this->_normalizeUrl( $file ) );
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
        return $this->view->baseUrl( str_replace( PUBLIC_PATH, '', $url ) );
    }
}
