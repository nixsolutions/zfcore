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
    /**
     * Index
     */
    public function indexAction()
    {
        $post = new Blog_Model_Post_Table();

        $source = $post->getPostsSourse();
        $paginator = Zend_Paginator::factory($source);

        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;
    }

    /**
    * Index
    */
    public function categoryAction()
    {
        if (!$alias = $this->_getParam('alias')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $category = new Blog_Model_Category_Manager();
        if (!$ctg = $category->getByAlias($alias)) {
            throw new Zend_Controller_Action_Exception('Blog not found');
        }

        $post = new Blog_Model_Post_Table();

        $source = $post->getPostsSourse($ctg->alias);
        $paginator = Zend_Paginator::factory($source);

        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;
        $this->view->category = $ctg;
    }

}
