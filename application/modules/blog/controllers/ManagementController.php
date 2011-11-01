<?php
/**
 * UsersController for admin module
 *
 * @category   Application
 * @package    Users
 * @subpackage Controller
 *
 * @version  $Id: ManagementController.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Blog_ManagementController extends Core_Controller_Action_Crud
{
    /**
     * init invironment
     *
     * @return void
     */
    public function init()
    {
        /* Initialize */
        parent::init();

        $this->_beforeGridFilter(array(
             '_addCheckBoxColumn',
             '_addAllTableColumns',
             '_prepareGrid',
             '_addEditColumn',
             '_addDeleteColumn',
             '_addCreateButton',
             '_addDeleteButton',
             '_showFilter'
        ));

    }

    public function postDispatch()
    {
        parent::postDispatch();

        if ('create' == $this->_getParam('action') || 'edit' == $this->_getParam('action')) {
            $this->_setDefaultScriptPath();
        }
    }

    /**
     * indexAction
     *
     */
    public function indexAction()
    {
        parent::indexAction();

        $this->view->headScript()->appendFile(
            $this->view->baseUrl('./modules/blog/scripts/management/index.js'
        ));
    }

    /**
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    protected function _getCreateForm()
    {
        return new Blog_Model_Post_Form_Admin_Create();
    }

    /**
     * _getEditForm
     *
     * return edit form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    protected function _getEditForm()
    {
        $form = new Blog_Model_Post_Form_Admin_Create();
        $form->addElement(new Zend_Form_Element_Hidden('id'));
        return $form;
    }

    /**
     * _getTable
     *
     * return manager for scaffolding
     *
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Blog_Model_Post_Table();
    }

    protected function _prepareGrid()
    {

        $this->grid
             ->removeColumn('body')
             ->removeColumn('userId')
             ->removeColumn('categoryId')
             ->removeColumn('views')
             ->removeColumn('replies')
             ->removeColumn('created')
             ->removeColumn('updated')
             ->removeColumn('published')
             ->setColumn('teaser', array(
                'formatter' => array($this, 'trimFormatter')
             ));
    }


}

