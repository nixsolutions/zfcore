<?php
/**
 * ManagementController for options module
 *
 * @category   Application
 * @package    Options
 * @subpackage Controller
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */
class Options_ManagementController extends Core_Controller_Action_Crud
{
    /**
     * _getCreateForm
     *
     * return create form for crud
     *
     * @return  Zend_Form
     */
    protected function _getCreateForm()
    {
        return new Options_Model_Options_Form_Create();
    }

    /**
     * _getEditForm
     *
     * return edit form for crud
     *
     * @return  Zend_Form
     */
    protected function _getEditForm()
    {
        return new Options_Model_Options_Form_Edit();
    }

    /**
     * _getTable
     *
     * return manager for crud
     *
     * @return  Options_Model_Options_Table
     */
    protected function _getTable()
    {
        return new Options_Model_Options_Table();
    }

    /**
     * custom grid filters
     *
     * @return void
     */
    protected function _prepareHeader()
    {
        $this->_addCreateButton();
    }

    /**
     * custom grid preparation
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->_addAllTableColumns();
        $this->_addEditColumn();
        $this->_addDeleteColumn();
    }
}




