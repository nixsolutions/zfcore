<?php
/**
 * UsersController for admin module
 *
 * @category   Application
 * @package    Users
 * @subpackage Controller
 * 
 * @version  $Id: ManagementController.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Forum_ManagementController extends Core_Controller_Action_Crud
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
             '_addAllTableColumns',
             '_prepareGrid',
             '_addCheckBoxColumn',
             '_addEditColumn',
             '_addDeleteColumn',
             '_addCreateButton',
             '_addDeleteAllButton',
             '_showFilter'
        ));
    }

    /**
     * indexAction
     *
     */
    public function indexAction()
    {
        $this->view->headScript()->appendFile(
            $this->view->baseUrl('./modules/forum/scripts/management/index.js'
        ));

        parent::indexAction();
    }

    /**
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    protected function _getCreateForm()
    {
        return new Forum_Model_Post_Form_Admin_Create();
    }
    
    /**
     * _getEditForm
     *
     * return edit form for scaffolding
     *
     * @return  Zend_Dojo_Form
     */
    protected function _getEditForm()
    {
        $form = new Forum_Model_Post_Form_Admin_Create();
        $form->addElement(new Zend_Form_Element_Hidden('id'));
        return $form;
    }

    /**
     * _getTable
     *
     * return manager for scaffolding
     *
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Forum_Model_Post_Table();
    }

    /**
     * change grid before render
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->grid
             ->removeColumn('categoryId')
             ->removeColumn('userId')
             ->removeColumn('views')
             ->removeColumn('comments')
             ->removeColumn('body')
             ->addColumn('body', array(
                'name' => ucfirst('body'),
                'type' => Core_Grid::TYPE_DATA,
                'index' => 'body',
                'formatter' => array($this, 'shorterFormatter')
             ));
    }

    /**
     * cut the message
     *
     * @param $value
     * @param $row
     * @return
     */
    public function shorterFormatter($value, $row)
    {
        if (strlen($row['body']) >= 200) {
            if (false !== ($breakpoint = strpos($row['body'], ' ', 200))) {
                if ($breakpoint < strlen($row['body']) - 1) {
                    $row['body'] = substr($row['body'], 0, $breakpoint) . ' ...';
                }
            }
        }
        return $row['body'];
    }

    /**
     * add create button
     *
     * @return void
     */
    protected function _addDeleteAllButton()
    {
        $link = '<a href="%s" class="button" id="delete-all-button">Delete All</a>';
                $url = $this->getHelper('url')->url(array(
            'action' => 'delete'
        ), 'default');
        $this->view->placeholder('grid_buttons')->create .= sprintf($link, $url);
    }


}

