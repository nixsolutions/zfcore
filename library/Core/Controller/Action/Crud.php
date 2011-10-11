<?php

/**
 * Core_Controller_Action_Crud
 *
 * @uses       Zend_Controller_Action
 * @category   Core
 * @package    Core_Controller
 * @subpackage Core_Controller_Action
 */
abstract class Core_Controller_Action_Crud extends Core_Controller_Action
{
    /**
     * init controller
     *
     * @return void
     */
    public function init()
    {
        $this->_isDashboard();

        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_viewRenderer = $this->_helper->getHelper('viewRenderer');
        $this->_redirector = $this->_helper->getHelper('redirector');

        /** load model before editing and deleting */
        $this->_before('_loadModel', array('only' => array('edit', 'delete')));

        /** change view script path specification */
        $this->_before('_changeViewScriptPathSpec', array('only' => array('index', 'grid', 'create', 'edit')));
    }

    /**
     * index
     *
     * @return void
     */
    public function indexAction()
    {

    }

    /**
     * grid
     *
     * @return void
     */
    public function gridAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }

        $this->view->grid = $this->_getGrid();
    }

    /**
     * create
     *
     * @return void
     */
    public function createAction()
    {
        $table = $this->_getTable();
        $form = $this->_getCreateForm()
            ->setAction($this->view->url());

        if ($this->_request->isPost() &&
            $form->isValid($this->_getAllParams())
        ) {
            $table->createRow($form->getValues())
                ->save();

            $this->_flashMessenger->addMessage('Successfully');
            $this->_redirector->direct('index');
        }

        $this->view->form = $form;
    }

    /**
     * edit
     *
     * @return void
     */
    public function editAction()
    {
        $form = $this->_getEditForm()
            ->setAction($this->view->url())
            ->setDefaults($this->model->toArray(true));

        if ($this->_request->isPost() &&
            $form->isValid($this->_getAllParams())
        ) {
            $this->model
                ->setFromArray($form->getValues())
                ->save();

            $this->_flashMessenger->addMessage('Successfully');
            $this->_redirector->direct('index');
        }

        $this->view->form = $form;
    }

    /**
     * load model
     *
     * @return void|bool
     */
    protected function _loadModel()
    {
        if (!$id = $this->_getParam('id')) {
            $this->_forwardNotFound();
            return false;
        }

        $table = $this->_getTable();

        if (!$model = $table->getById($id)) {
            $this->_forwardNotFound();
            return false;
        }

        $this->model = $model;
    }

    /**
     * change view script path specification
     *
     * @return void
     */
    protected function _changeViewScriptPathSpec()
    {
        $this->_viewRenderer->setViewScriptPathSpec('crud/:action.:suffix');
    }

    /**
     * delete
     *
     * @return void
     */
    public function deleteAction()
    {
        $this->_helper->json($this->model->delete());
    }

    /**
     * get create form
     *
     * @abstract
     * @return Zend_Form
     */
    abstract protected function _getCreateForm();

    /**
     * get edit form
     *
     * @abstract
     * @return Zend_Form
     */
    abstract protected function _getEditForm();

    /**
     * get grid
     *
     * @return Core_Grid
     */
    protected function _getGrid()
    {
        $table = $this->_getTable();
        $cols = $table->info(Zend_Db_Table::COLS);

        $grid = new Core_Grid();
        $grid->setSelect($this->_getSource())
            ->setOrder($this->_getParam('orderColumn', 'id'), $this->_getParam('orderDirection', 'asc'))
            ->setCurrentPageNumber($this->_getParam('page', 1))
            ->setItemCountPerPage(5);

        if ($this->_getParam('filterColumn')) {
            $grid->setFilter($this->_getParam('filterColumn'), $this->_getParam('filterValue'));
        }

        foreach ($cols as $col) {
            $grid->addColumn($col, array(
                'name' => ucfirst($col),
                'type' => Core_Grid::TYPE_DATA,
                'index' => $col
            ));
        }

        $grid->addColumn('edit', array(
            'name' => 'Edit',
            'formatter' => array($this, 'editLinkFormatter')
        ));

        $grid->addColumn('delete', array(
            'name' => 'Delete',
            'formatter' => array($this, 'deleteLinkFormatter')
        ));

        return $grid;
    }

    /**
     * edit link formatter
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function editLinkFormatter($value, $row)
    {
        $link = '<a href="%s">Edit</a>';
        $url = $this->getHelper('url')->url(array(
            'action' => 'edit',
            'id' => $row['id']
        ), 'default');

        return sprintf($link, $url);
    }

    /**
     * delete link formatter
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function deleteLinkFormatter($value, $row)
    {
        $link = '<a href="%s">Delete</a>';
        $url = $this->getHelper('url')->url(array(
            'action' => 'delete',
            'id' => $row['id']
        ), 'default');

        return sprintf($link, $url);
    }

    /**
     * get table
     *
     * @abstract
     * @return Core_Db_Table_Abstract
     */
    abstract protected function _getTable();

    /**
     * get source
     *
     * @return Zend_Db_Select
     */
    protected function _getSource()
    {
        return $this->_getTable()->select();
    }

    /**
     * set default script path
     *
     * @return Core_Controller_Action_Crud
     */
    protected function _setDefaultScriptPath()
    {
        $this->_viewRenderer->setViewScriptPathSpec(':controller/:action.:suffix');
        return $this;
    }
}
