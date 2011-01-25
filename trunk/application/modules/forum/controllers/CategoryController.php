<?php
/**
 * CategoryController for forum module
 *
 * @category   Application
 * @package    Forum
 * @subpackage Controller
 *
 * @version  $Id: CategoryController.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Forum_CategoryController extends Core_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        parent::init();
        $this->_module = $this->getRequest()->getModuleName();
    }

    public function indexAction()
    {
        switch ($this->_module) {
            case 'blog':
                $this->_forward('blog');
                break;
            case 'forum':
            default:
                $this->_forward('forum');
                break;
        }
    }
    
    public function forumAction()
    {
        $catId = $this->_getParam('id', 0);
        $page = $this->_getParam('page', 1, 'integer');
        $ctg = new Forum_Model_Category_Manager();
        $post = new Forum_Model_Post_Manager();
        
        $cats = $ctg->getCategories($catId);
        
        $source = $post->getPostsSourse($catId);
        $paginator = Zend_Paginator::factory($source);
        
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);
        
        $this->view->cats = $cats;
        $this->view->paginator = $paginator;
    }
    
    public function blogAction()
    {
        $catId = $this->_getParam('id');
        $page = $this->_getParam('page', 1, 'integer');
        $post = new Forum_Model_Post_Manager();
        $ctg = new Forum_Model_Category_Manager();
        $cats = $ctg->getTreeCategories();

        $source = $post->getLastPostsSourse($catId);
        $paginator = Zend_Paginator::factory($source);

        $paginator->setItemCountPerPage(5);
        $paginator->setCurrentPageNumber($page);
       
        $this->view->cats = $cats;
        $this->view->paginator = $paginator;
    }

}
