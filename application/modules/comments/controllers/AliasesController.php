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
                'formatter' => array($this, 'showCommentsFormatter'),
                'attribs'   => array('width' => '60px')
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
        $link = '<a href="%s" class="btn">Show</a>';
        $url = $this->getHelper('url')->url(
            array(
                'controller' => 'management',
                'action' => 'index',
                'alias' => $row['id'],
            ),
            'default'
        );

        return sprintf($link, $url);
    }

    /**
     * custom grid filters
     *
     * @return void
     */
    protected function _prepareHeader()
    {
        $this->_addCreateButton();
        $this->_addDeleteButton();
    }

    /**
     * Prepare grid - remove not needed columns
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->_addCheckBoxColumn();
        $this->_grid->setColumn(
                        'alias',
                        array(
                            'name'  => 'Section Alias',
                            'type'  => Core_Grid::TYPE_DATA,
                            'index' => 'alias'
                        )
                    );
        $this->_grid->setColumn(
                        'options',
                        array(
                            'name'  => 'Options',
                            'type'  => Core_Grid::TYPE_DATA,
                            'index' => 'options',
                            'formatter' => array($this, 'toStringFormatter')
                        )
                    );
        $this->_addCreatedColumn();
        $this->_addShowCommentsColumn();
        $this->_addEditColumn();
        $this->_addDeleteColumn();
        $this->_grid->removeColumn('aliasId');

//        if ($this->_alias && !$this->_alias->isTitleDisplayed()) {
//            $this->_grid->removeColumn('title');
//        }
    }

    /**
     * toStringFormatter
     *
     * @param $value
     * @param $row
     * @return string
     */
    function toStringFormatter($value, $row)
    {
        $res = json_decode($value);
        if ($res) {
            return join(', ', $res);
        } else {
            return '';
        }
    }
}