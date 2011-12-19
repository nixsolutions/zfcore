<?php
/**
 * Comments_AliasesController for Comments module
 *
 * @category   Application
 * @package    Comments
 * @subpackage Controller
 *
 * @version  $Id: AliasesController.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_AliasesController extends Core_Controller_Action_Crud
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
//             '_prepareGrid',
             '_addEditColumn',
             '_addDeleteColumn',
             '_addCreateButton',
             '_addDeleteButton',
             '_showFilter'
        ));

    }
    
    /**
     * Get table
     *
     * @return Pages_Model_Page_Table
     */
    protected function _getTable()
    {
        return new Comments_Model_CommentAlias_Table();
    }
    
    /**
     * get create form
     *
     * @return Comments_Model_Comment_Form_Create
     */
    protected function _getCreateForm()
    {
        return new Comments_Model_CommentAlias_Form_Create();
    }

    /**
     * get edit form
     *
     * @return Comments_Model_Comment_Form_Create
     */
    protected function _getEditForm()
    {
        return new Comments_Model_CommentAlias_Form_Edit();
    }
}