<?php
/**
 * PagesController for Admin module
 *
 * @category   Application
 * @package    Pages
 * @subpackage Controller
 * 
 * @version  $Id: ManagementController.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Pages_ManagementController extends Core_Controller_Action_Scaffold
{
    /**
     * Init environmonet
     */
    public function init() 
    {
        /* Initialize */
        parent::init();
        
        /* is Dashboard Controller */
        $this->_isDashboard();
    }

    /**
     * indexAction
     *
     * Index action in Pages Controller
     *
     * @access public
     * @created Wed Aug 06 13:09:14 EEST 2008
     */
    public function indexAction()
    {
        // see view script
        // use dojox.grid.DataGrid
    }

    /**
     * createAction
     *
     * @return void
     */
//    function createAction()
//    {
//        parent::createAction();
//        $this->_setDefaultScriptPath();
//    }

    /**
     * createAction
     *
     * @return void
     */
//    function editAction()
//    {
//        parent::editAction();
//        $this->_setDefaultScriptPath();
//    }

    /**
     * upload image
     *
     * @return void
     */
    public function uploadAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $type = strtolower($_FILES['file']['type']);
        $allowed = array(
            'image/png',
            'image/jpg',
            'image/jpeg',
            'image/pjpeg',
            'image/gif'
        );

        if (in_array($type, $allowed)) {
            $path = realpath(APPLICATION_PATH . '/../public/uploads') . '/' . $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $path);
            echo $this->view->baseUrl('/uploads/' . $_FILES['file']['name']);
        }
    }

    /**
     * _getManager
     *
     * return manager for scaffolding
     *
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Pages_Model_Page_Table();
    }

    /**
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Form
     */
    protected function _getCreateForm()
    {
        return new Pages_Form_Create();
    }

    /**
     * _getEditForm
     *
     * return edit form for scaffolding
     *
     * @return  Zend_Form
     */
    protected function _getEditForm()
    {
        return new Pages_Form_Edit();
    }
}
