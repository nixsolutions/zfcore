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
     * init controller
     *
     * @return void
     */
    public function init()
    {
        /* Initialize */
        parent::init();

        $this->_beforeGridFilter(array('_addAllTableColumns','_addEditColumn', '_addDeleteColumn'));
    }

    /**
     * _getCreateForm
     *
     * return create form for crud
     *
     * @return  Zend_Dojo_Form
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
     * @return  Zend_Dojo_Form
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
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Options_Model_Options_Table();
    }
}




