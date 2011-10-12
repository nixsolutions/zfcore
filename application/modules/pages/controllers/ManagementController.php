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
     * @return Zend_Form
     */
    protected function _getCreateForm()
    {
        return new Pages_Form_Create();
    }

    /**
     * get edit form
     *
     * @return Zend_Form
     */
    protected function _getEditForm()
    {
        return new Pages_Form_Edit();
    }

    /**
     * get grid
     *
     * @return Core_Grid
     */
    protected function _getGrid()
    {
        return parent::_getGrid()
            ->removeColumn('pid')
            ->removeColumn('user_id');
    }
}
