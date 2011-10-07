<?php
/**
 * PagesController for Admin module
 *
 * @category   Application
 * @package    Pages
 * @subpackage Controller
 */
class Pages_Management2Controller extends Core_Controller_Action
{
    /**
     * init environment
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
     *
     * @return void
     */
    public function indexAction()
    {

    }

    /**
     * grid
     *
     * @return void
     */
    public function gridAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }

        $grid = new Core_Grid();
        $grid->setSelect($this->_getSource());
        $grid->setOrder($this->_getParam('orderColumn', 'id'), $this->_getParam('orderDirection', 'asc'));
//        $grid->setFilter('id', '1');
        $grid->setCurrentPageNumber($this->_getParam('page', 1));
        $grid->setItemCountPerPage(5);

        $grid->addColumn('id', array(
            'name' => 'Id',
            'index' => 'id',
            'formatter' => array($this, 'formatter')
        ));

        $grid->addColumn('label', array(
            'name' => 'Label',
            'index' => 'label',
            'formatter' => array($this, 'formatter')
        ));

        $grid->addColumn('title', array(
            'name' => 'Title',
            'index' => 'title',
            'formatter' => array($this, 'formatter')
        ));

        $this->view->grid = $grid;
    }

    public function formatter($value, $row)
    {
        return $value . '_formatted';
    }

    /**
     * get source
     *
     * @return Zend_Db_Select
     */
    protected function _getSource()
    {
        $table = new Pages_Model_Page_Table();
        $select = $table->getAdapter()->select()
            ->from('menu');

        return $select;
    }
}
