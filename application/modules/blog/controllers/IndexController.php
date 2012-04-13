<?php
/**
 * IndexController for Blog module
 *
 * @category   Application
 * @package    Blog
 * @subpackage Controller
 *
 * @version  $Id: IndexController.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Blog_IndexController extends Core_Controller_Action
{
    protected $_itemsPerPage = 10;


    /**
     * Index
     */
    public function indexAction()
    {
        $post = new Blog_Model_Post_Table();

        $source = $post->getSelect();
        $paginator = Zend_Paginator::factory($source);
        $paginator->getView()->route = 'blog';
        $paginator->setItemCountPerPage($this->_itemsPerPage);
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;
    }

    /**
     * View blog category
     */
    public function categoryAction()
    {
        if (!$alias = $this->_getParam('alias')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $category = new Blog_Model_Category_Manager();
        if (!$row = $category->getByAlias($alias)) {
            throw new Zend_Controller_Action_Exception('Blog not found');
        }

        $post = new Blog_Model_Post_Table();

        $source = $post->getSelect($row);
        $paginator = Zend_Paginator::factory($source);
        $paginator->getView()->route = 'blogcategory';
        $paginator->setItemCountPerPage($this->_itemsPerPage);
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;
        $this->view->category = $row;

        $this->render('index');
    }

    /**
     * View blog author
     */
    public function authorAction()
    {
        if (!$login = $this->_getParam('login')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $users = new Users_Model_Users_Table();
        if (!$row = $users->getByLogin($login)) {
            throw new Zend_Controller_Action_Exception('Blog not found');
        }

        $post = new Blog_Model_Post_Table();

        $source = $post->getSelect(null, $row->id);
        $paginator = Zend_Paginator::factory($source);
        $paginator->getView()->route = 'blogauthor';
        $paginator->setItemCountPerPage($this->_itemsPerPage);
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;
        $this->view->author = $row;

        $this->render('index');
    }
}
