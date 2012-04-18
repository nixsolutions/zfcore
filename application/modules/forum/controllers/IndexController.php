<?php
/**
 * IndexController for forum module
 *
 * @category   Application
 * @package    Forum
 * @subpackage Controller
 */
class Forum_IndexController extends Core_Controller_Action
{
    /**
     * Index
     */
    public function indexAction()
    {
        $manager = new Forum_Model_Category_Manager();

        $this->view->categories = $manager->getRoot()
                                          ->getAllChildren(null, 'path');

        $posts = new Forum_Model_Post_Table();

        $this->view->posts = $posts->getLastPosts();
    }

    /**
     * Category
     */
    public function categoryAction()
    {
        if (!$alias = $this->_getParam('alias')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $manager = new Forum_Model_Category_Manager();
        if (!$category = $manager->getByAlias($alias)) {
            throw new Zend_Controller_Action_Exception('Category not found');
        }

        $this->view->category = $category;

        $this->view->categories = $category->getAllChildren();

        $posts = new Forum_Model_Post_Table();

        $this->view->posts = $posts->getLastPosts();

        $select = $posts->getPostsSelect($category->id);
        $paginator = Zend_Paginator::factory($select);

        $paginator->setItemCountPerPage(20);
        $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->view->paginator = $paginator;
    }
}
