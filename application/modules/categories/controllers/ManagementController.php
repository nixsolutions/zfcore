<?php
/**
 * ManagementController for module
 *
 * @category   Application
 * @package    Categories
 * @subpackage Controller
 */
class Categories_ManagementController extends Core_Controller_Action_Scaffold
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

        /* is Dashboard Controller */
        $this->_isDashboard();
    }

    /**
     * indexAction
     */
    public function indexAction()
    {

    }

    /**
     * createAction
     *
     * @return void
     */
    function createAction()
    {
        parent::createAction();
        $this->_setDefaultScriptPath();
    }

    /**
     * createAction
     *
     * @return void
     */
    function editAction()
    {
        parent::editAction();
        $this->_setDefaultScriptPath();
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
        return new Categories_Form_Category_Create();
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
        $form = new Categories_Form_Category_Edit();
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
        return new Categories_Model_Category_Table();
    }

    /**
    * getDojoGrid
    *
    * get list of static pages
    *
    * @param   Zend_Request $Request
    * @return  null|array
    */
    public function storeAction()
    {
        $start  = $this->_getParam('start');
        $count  = $this->_getParam('count');
        $sort   = $this->_getParam('sort', 'path');
        $field  = $this->_getParam('field');
        $filter = $this->_getParam('filter');

        // sort data
        //   field  - ASC
        //   -field - DESC
        if ($sort && ltrim($sort, '-')
            && in_array(ltrim($sort, '-'), $this->_table->info(Zend_Db_Table::COLS))
        ) {
            if (strpos($sort, '-') === 0) {
                $order = ltrim($sort, '-') .' '. Zend_Db_Select::SQL_DESC;
            } else {
                $order = $sort  .' '.  Zend_Db_Select::SQL_ASC;
            }
        }

        // Use LIKE for filter
        if ($field && in_array($field, $this->_table->info(Zend_Db_Table::COLS))
            && $filter && $filter != '*') {

            $filter = str_replace('*', '%', $filter);
            $filter = $this->_table->getAdapter()->quote($filter);

            $where = $field .' LIKE '. $filter;
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
            foreach ($data as $row) {
                $row->title = str_repeat("-", $row->level) . ' ' . $row->title;
            }
            $data = new Zend_Dojo_Data($primary, $data->toArray());
            $data->setMetadata('numRows', $total);

            $this->_helper->json($data);
        } else {
            $this->_helper->json(false);
        }
    }
}