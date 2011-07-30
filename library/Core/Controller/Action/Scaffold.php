<?php
/**
 * Class Core_Controller_Actions_Scaffold
 *
 * Scaffold realization
 *
 * @uses     Zend_Controller_Action
 *
 * @category Core
 * @package  Core_Controller
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 *
 * @version  $Id: Scaffold.php 206 2010-10-20 10:55:55Z AntonShevchuk $
 */
abstract class Core_Controller_Action_Scaffold extends Core_Controller_Action
{
    /**
     * @var Core_Db_Table_Abstract
     */
    protected $_table;

    /**
     * Init controller plugins
     *
     */
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_viewRenderer = $this->_helper->getHelper('viewRenderer');

        $this->_table = $this->_getTable();
    }

    /**
     * _getTable
     *
     * return DBTable for scaffolding
     *
     * @return  Core_Db_Table_Abstract
     */
    abstract protected function _getTable();

    /**
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    abstract protected function _getCreateForm();

    /**
     * _getEditForm
     *
     * return edit form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    abstract protected function _getEditForm();

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
        $this->_viewRenderer->setViewScriptPathSpec(':controller/:action.:suffix');
        return $this;
    }

    /**
     * createAction
     *
     * create page instance
     *
     * @return  void
     */
    public function createAction()
    {

        $createForm = $this->_getCreateForm()
                ->setAction($this->view->url());

        if ($this->_request->isPost() &&
                $createForm->isValid($this->_getAllParams())
        ) {
            $model = $this->_table->createRow();
            $model->setFromArray($createForm->getValues());
            $model->save();
            $this->_flashMessenger->addMessage('Successfully!');

            $this->_helper->getHelper('redirector')->direct('index');

        } else {
            $this->view->createForm = $createForm;
            $this->_viewRenderer
                    ->setViewBasePathSpec('dashboard/scripts')
                    ->setViewScriptPathSpec('scaffold/:action.:suffix'); //must be here
        }
    }

    /**
     * editAction
     *
     * edit page instance
     *
     * @return  void
     */
    public function editAction()
    {

        $editForm = $this->_getEditForm()
                ->setAction($this->view->url());

        $rows = $this->_table->find($this->_getParam('id'));

        if (!sizeof($rows)) {
            //$module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();

            return $this->_forward('notfound', 'error');
        } else {
            $model = $rows->current();
        }

        if ($this->_request->isPost() &&
                $editForm->isValid($this->_getAllParams())
        ) {
            // valid
            $model->setFromArray($editForm->getValues())
                    ->save();

            $this->_flashMessenger->addMessage('Successfully!');
            $this->_helper->getHelper('redirector')->direct('index');
        } else {
            // check if there is data in form
            if (!in_array(true, $editForm->getValues())) {
                $editForm->setDefaults($model->toArray(true));
            }
            $this->view->editForm = $editForm;
        }

        $dashboard = Zend_Controller_Front::getInstance()->getModuleDirectory('dashboard');


        $this->_viewRenderer
                ->setViewBasePathSpec($dashboard . '/views')
                ->setViewScriptPathSpec('scaffold/:action.:suffix'); //must be here
    }


    /**
     * deleteAction
     *
     * @return  void
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $this->_helper->json($this->_table->deleteById($id));
    }

    /**
     * get list of static pages
     *
     * @return  null|array
     */
    public function storeAction()
    {
        $start = $this->_getParam('start');
        $count = $this->_getParam('count');
        $sort = $this->_getParam('sort');
        $field = $this->_getParam('field');
        $filter = $this->_getParam('filter');

        // sort data
        //   field  - ASC
        //   -field - DESC
        if ($sort
                && ltrim($sort, '-')
                && in_array(ltrim($sort, '-'), $this->_table->info(Zend_Db_Table::COLS))
        ) {
            if (strpos($sort, '-') === 0) {
                $order = ltrim($sort, '-') . ' ' . Zend_Db_Select::SQL_DESC;
            } else {
                $order = $sort . ' ' . Zend_Db_Select::SQL_ASC;
            }
        }

        // Use LIKE for filter
        if ($field
                && in_array($field, $this->_table->info(Zend_Db_Table::COLS))
                && $filter
                && $filter != '*'
        ) {

            $filter = str_replace('*', '%', $filter);
            $filter = $this->_table->getAdapter()->quote($filter);

            $where = $field . ' LIKE ' . $filter;
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        switch ($db) {
            case $db instanceof Zend_Db_Adapter_Pdo_Mysql :
                $select = $this->_table->select();
                $select->from(
                    $this->_table->info(Zend_Db_Table::NAME),
                    new Zend_Db_Expr('SQL_CALC_FOUND_ROWS *')
                );
                if (isset($where)) {
                    $select->where($where);
                }
                if (isset($order)) {
                    $select->order($order);
                }
                $select->limit($count, $start);
                if ($data = $this->_table->fetchAll($select)) {
                    $total = $this->_table->getAdapter()
                            ->fetchOne(
                        $this->_table->getAdapter()->select()->from(null, new Zend_Db_Expr('FOUND_ROWS()'))
                    );
                }
                break;
            default:
                $select = $this->_table->select();
                $select->from(
                    $this->_table->info(Zend_Db_Table::NAME),
                    new Zend_Db_Expr('COUNT(*) as c')
                );
                if (isset($where)) {
                    $select->where($where);
                }
                if ($total = $this->_table->fetchRow($select)) {
                    $total = $total->c;
                    $select = $this->_table->select();
                    $select->from($this->_table->info(Zend_Db_Table::NAME));
                    if (isset($where)) {
                        $select->where($where);
                    }
                    if (isset($order)) {
                        $select->order($order);
                    }
                    $select->limit($count, $start);
                    $data = $this->_table->fetchAll($select);
                }
                break;
        }


        if ($total) {
            $primary = $this->_table->getPrimary();
            if (is_array($primary)) {
                $primary = current($primary);
            }
            $data = new Zend_Dojo_Data($primary, $data->toArray());
            $data->setMetadata('numRows', $total);

            $this->_helper->json($data);
        } else {
            $this->_helper->json(false);
        }
    }
}