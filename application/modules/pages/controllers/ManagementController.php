<?php
/**
 * PagesController for Admin module
 *
 * @category   Application
 * @package    Pages
 * @subpackage Controller
 */
class Pages_ManagementController extends Core_Controller_Action_Crud
{
    /**
     * init controller
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->_beforeGridFilter('_addAllTableColumns');
        $this->_beforeGridFilter('_prepareGrid');
        $this->_beforeGridFilter(array('_addEditColumn', '_addDeleteColumn'));
    }

    /**
     * get table
     *
     * @return Pages_Model_Page_Table
     */
    protected function _getTable()
    {
        return new Pages_Model_Page_Table();
    }

    /**
     * get create form
     *
     * @return Pages_Form_Create
     */
    protected function _getCreateForm()
    {
        return new Pages_Form_Create();
    }

    /**
     * get edit form
     *
     * @return Pages_Form_Edit
     */
    protected function _getEditForm()
    {
        return new Pages_Form_Edit();
    }

    /**
     * custom grid preparation
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->grid
            ->setDefaultOrder('title')
            ->removeColumn('pid')
            ->removeColumn('user_id');
    }
}
