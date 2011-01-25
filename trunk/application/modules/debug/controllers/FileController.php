<?php
/**
 * FileController for debug module
 *
 * @category   Application
 * @package    Debug
 * @subpackage Controller
 * 
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */


class Debug_FileController extends Core_Controller_Action
{
    /**
     * Init controller plugins
     *
     */
    public function init()
    {
        /* Initialize action controller here */
        parent::init();
        /* is Dashboard Controller */
        $this->_isDashboard();

        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_viewRenderer   = $this->_helper->getHelper('viewRenderer');

    }

     /**
     * indexAction
     *
     */
    public function indexAction()
    {

    }

    /**
     * _setDefaultScriptPath
     *
     * @return  void
     */
    protected function _setDefaultBasePath()
    {
        $this->_viewRenderer->setViewBasePathSpec(':moduleDir/views');
        return $this;
    }

    /**
     * _setDefaultScriptPath
     *
     * @return  void
     */
    protected function _setDefaultScriptPath()
    {
        $this->_viewRenderer->setViewScriptPathSpec(
            ':controller/:action.:suffix'
        );
        return $this;
    }

    /**
     * loadAction
     *
     * load file content for Ajax query
     *
     * @return  void
     */
    public function loadAction()
    {
        $manager = new Debug_Model_File_Manager();
        $files = $manager->createFileArray($this->_getParam('id'));

        $this->_helper->json($files);

        return $this;
    }

    /**
     * lazyTreeAction
     *
     * get json data for Lazy Folder Tree
     *
     *
     * @return  json data for Folder Tree
     */
    public function lazyAction()
    {
        $type = $this->_request->getParam('type', null);
        $node = $this->_request->getParam('node', null);
        $manager = new Debug_Model_File_Manager();

        if (!empty($type)) {
            $tree = $manager->createLazyTreeArray();
        } 
        if (!empty($node)) {
            $tree = $manager->createLazyTreeArray($node);
        }

        if ($this->_request->getParam('format')) {
            $this->_helper->json($tree);
        } else {
            $this->_helper->json(false);
        }
        return $this;
    }
}


