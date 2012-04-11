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
     * Declare the source used to fetch the comments
     *
     * @return Core_Grid_Adapter_AdapterInterface
     */
    protected function _getSource()
    {
        return new Core_Grid_Adapter_Select(
            $this->_getTable()
                 ->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                 ->setIntegrityCheck(false)
                 ->joinLeft('users','users.id=pages.userId', array('login'))
        );
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
        $this->_addCheckBoxColumn();
        $this->_grid->setColumn(
                                'login',
                                array(
                                    'name'  => 'Author',
                                    'type'  => Core_Grid::TYPE_DATA,
                                    'index' => 'login',
                                    'attribs' => array('width'=>'120px')
                                )
                            );

        $this->_grid->setColumn(
                        'title',
                        array(
                            'name'  => 'Title',
                            'type'  => Core_Grid::TYPE_DATA,
                            'index' => 'title',
                            'formatter' => array($this, 'trimFormatter'),
                        )
                    );

        $this->_grid->setColumn(
                        'description',
                        array(
                            'name'  => 'Description',
                            'type'  => Core_Grid::TYPE_DATA,
                            'index' => 'description',
                            'formatter' => array($this, 'trimFormatter'),
                        )
                    );

        $this->_addCreatedColumn();
        $this->_addEditColumn();
        $this->_addDeleteColumn();
    }
}
