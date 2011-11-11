<?php

/**
 * IndexController for asset module
 *
 * @category   Application
 * @package    Asset
 * @subpackage Controller
 */
class Asset_IndexController extends Core_Controller_Action
{
    /**
     * build
     *
     * @throws Core_Exception
     * @return void
     */
    public function buildAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $file = $this->_getParam('file');
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        /** @var $asset Core_Asset */
        $asset = Zend_Registry::get('Core_Asset');

        if ($ext == 'css') {
            $asset->buildStylesheets();
            $buildFile = $asset->getStylesheetBuild();
            $contentType = 'text/css';
        } elseif ($ext == 'js') {
            $asset->buildJavascripts();
            $buildFile = $asset->getJavascriptBuild();
            $contentType = 'text/javascript';
        } else {
            throw new Core_Exception('"' . $ext . '" is not allowed');
        }

        $this->getResponse()
            ->setHeader('Content-Type', $contentType)
            ->setHeader('Content-Length', filesize($buildFile))
            ->setBody(file_get_contents($buildFile));
    }
}
