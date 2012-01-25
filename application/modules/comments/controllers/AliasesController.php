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
     * Initialize
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        // setup the grid
        $this->_beforeGridFilter(
            array(
                '_addCheckBoxColumn',
                '_addAllTableColumns',
                '_addShowCommentsColumn',
                '_addEditColumn',
                '_addDeleteColumn',
                '_addCreateButton',
                '_addDeleteButton',
                '_showFilter'
            )
        );
    }
    
    /**
     * Declare the DB table
     *
     * @return Comments_Model_CommentAlias_Table
     */
    protected function _getTable()
    {
        return new Comments_Model_CommentAlias_Table();
    }
    
    /**
     * Declare create form
     *
     * @return Comments_Model_Comment_Form_Create
     */
    protected function _getCreateForm()
    {
        return new Comments_Model_CommentAlias_Form_Create();
    }

    /**
     * Declare edit form
     *
     * @return Comments_Model_Comment_Form_Create
     */
    protected function _getEditForm()
    {
        return new Comments_Model_CommentAlias_Form_Edit();
    }
    
    /**
     * Add a "show" column to the grid
     *
     * @return void
     */
    public function _addShowCommentsColumn()
    {
        $this->_grid->setColumn(
            'show',
            array(
                'name' => 'Show',
                'formatter' => array($this, 'showCommentsFormatter')
            )
        );
    }

    /**
     * Formatter for the "show" column value
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function showCommentsFormatter($value, $row)
    {
        $link = '<a href="%s" class="Show">Show</a>';
        $url = $this->getHelper('url')->url(
            array(
                'controller' => 'management',
                'action' => 'index',
                'alias' => $row['id']
            ),
            'default'
        );

        return sprintf($link, $url);
    }
}