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
     * custom grid filters
     *
     * @return void
     */
    protected function _prepareHeader()
    {
        $this->_addCreateButton();
        $this->_addFilter('title', 'Title');
        $this->_addFilter('alias', 'Alias');
    }

    /**
     * custom grid preparation
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->_addAllTableColumns();


        $this->_grid
            ->setDefaultOrder('title')
            ->removeColumn('pid')
            ->removeColumn('user_id')
            ->removeColumn('content')
            ->removeColumn('keywords')
        ;

        $this->_addEditColumn();
        $this->_addDeleteColumn();
    }
}
