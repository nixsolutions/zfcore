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
class Blog_ManagementController extends Core_Controller_Action_Crud
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
        parent::indexAction();

        $this->view->headScript()->appendFile(
            $this->view->baseUrl('./modules/blog/scripts/management/index.js'
        ));
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
        return new Blog_Model_Post_Form_Admin_Create();
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
        $form = new Blog_Model_Post_Form_Admin_Create();
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
        return new Blog_Model_Post_Table();
    }

    protected function _prepareGrid()
    {

        $this->grid
             ->removeColumn('body')
             ->removeColumn('userId')
             ->removeColumn('categoryId')
             ->removeColumn('views')
             ->removeColumn('replies')
             ->removeColumn('created')
             ->removeColumn('updated')
             ->removeColumn('published')
             ->setColumn('teaser', array(
                'name' => ucfirst('teaser'),
                'type' => Core_Grid::TYPE_DATA,
                'index' => 'teaser',
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
        if (strlen($row['teaser']) >= 200) {
            if (false !== ($breakpoint = strpos($row['teaser'], ' ', 200))) {
                if ($breakpoint < strlen($row['teaser']) - 1) {
                    $row['teaser'] = substr($row['teaser'], 0, $breakpoint) . ' ...';
                }
            }
        }
        return $row['teaser'];
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

