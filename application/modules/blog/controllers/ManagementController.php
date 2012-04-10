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
     * module statistic
     *
     * @return void
     */
    public function statsAction()
    {
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $this->view->totalPosts = $adapter->fetchOne('SELECT COUNT(*) FROM `blog_post`');
        $this->view->publicPosts = $adapter->fetchOne(
            'SELECT COUNT(*) FROM `blog_post` WHERE `status` = ?',
            array(Blog_Model_Post::STATUS_PUBLISHED)
        );
    }

    /**
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Form
     */
    protected function _getCreateForm()
    {
        return new Blog_Form_Admin_Create();
    }

    /**
     * _getEditForm
     *
     * return edit form for scaffolding
     *
     * @return  Zend_Form
     */
    protected function _getEditForm()
    {
        $form = new Blog_Form_Admin_Create();
        $form->addElement(new Zend_Form_Element_Hidden('id'));
        return $form;
    }

    /**
     * _getTable
     *
     * return manager for scaffolding
     *
     * @return  Blog_Model_Post_Table
     */
    protected function _getTable()
    {
        return new Blog_Model_Post_Table();
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
     * setup grid
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->_addCheckBoxColumn();
        $this->_addAllTableColumns();
        $this->_grid
             ->removeColumn('alias')
             ->removeColumn('body')
             ->removeColumn('userId')
             ->removeColumn('categoryId')
             ->removeColumn('views')
             ->removeColumn('replies')
             ->removeColumn('created')
             ->removeColumn('updated')
             ->removeColumn('published')
             ->removeColumn('teaser');
    }


}

